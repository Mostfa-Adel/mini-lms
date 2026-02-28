# Architecture

## High-Level Structure

The application follows Laravel MVC with **Action classes** for core flows, **Events and queued Listeners** for side effects (emails, certificates), and **Policies** for authorization.

```
┌─────────────┐     ┌──────────────────┐     ┌─────────────┐
│   Routes    │────▶│ Livewire/Blade   │────▶│   Actions   │
│  (web.php)  │     │   Components     │     │ (Enroll,    │
└─────────────┘     └──────────────────┘     │  Complete,  │
        │                      │              │  Certificate)│
        │                      │              └──────┬──────┘
        ▼                      ▼                     │
┌─────────────┐     ┌──────────────────┐            │
│  Policies  │     │  Models (Course,  │◀───────────┘
│ (Course,   │     │  Lesson, etc.)    │
│  Lesson,   │     └──────────────────┘
│ Enrollment)│              │
└─────────────┘              ▼
                   ┌──────────────────┐
                   │ Events & Queued  │
                   │ Listeners        │
                   │ (Welcome,        │
                   │  Certificate,   │
                   │  Completion)    │
                   └──────────────────┘
```

## Data Flow

### Enrollment

1. User clicks "Enroll" on a course page.
2. `ShowCourse` Livewire component calls `EnrollUserInCourseAction`.
3. Action runs in a **DB transaction** with `lockForUpdate()` on the enrollment query to avoid duplicate rows under concurrent requests.
4. **Unique constraint** `(user_id, course_id)` on `enrollments` guarantees at most one enrollment per user per course.
5. Policy `CoursePolicy::enroll` ensures the user is authenticated and the course is published.

### Lesson completion and certificate

1. **First view of a lesson (enrolled user):** `RecordLessonStartedAction` runs from `ShowLesson::mount`: creates or updates `lesson_progress` with `started_at` only; `completed_at` stays null until the user explicitly marks the lesson completed. So “first review” does not set completion.
2. User marks a lesson completed (after confirmation modal, or when video ends).
3. `MarkLessonCompletedAction` runs in a transaction: creates/updates `lesson_progress`, sets `completed_at` (and `started_at` if not already set).
4. Action checks if all lessons in the course are now completed; if yes, dispatches **CourseCompleted** event (once per completion).
5. **IssueCertificateListener** (sync) runs: `IssueCertificateAction::execute` uses `firstOrCreate` on `(user_id, course_id)` so only one certificate is ever created, and sets **enrollment.completed_at** so we have a denormalized “course completed” flag.
6. **SendCompletionEmailListener** (queued) runs: sends completion email and sets `completion_email_sent` on the certificate so the email is sent only once.

### Concurrency

- **Enrollment**: Transaction + unique constraint + optional `lockForUpdate` for idempotency. **enrollment.completed_at** is set when a certificate is issued so “course completed” is a simple flag, not recomputed from lesson progress every time.
- **Progress**: `updateOrCreate` / unique constraint on `(user_id, lesson_id)`. `started_at` is set on first view; `completed_at` only when the user marks the lesson completed.
- **Certificate**: `firstOrCreate` in listener; unique constraint on `(user_id, course_id)`.
- **Completion email**: Flag `completion_email_sent` with locking in the listener to prevent duplicate sends.

## Caching and performance

- **No Redis caching** in the app: list and lookup performance rely on **indexed DB queries** (e.g. unique index on `courses.slug`, index on `courses.is_published`, foreign keys on `certificates.user_id`). Course-by-slug uses a single indexed lookup; explore and certificates use straightforward queries with indexes.
- **Cache driver**: Default is `database` (Laravel's `cache` table). The queue worker uses it for the `queue:restart` signal. Set `CACHE_STORE=redis` in `.env` if you prefer Redis for general cache; the app does not require it.

## Docker Services

| Service | Role |
|--------|------|
| **app** | PHP-FPM; entrypoint runs composer, migrate, npm, then starts FPM. Code is mounted as `.:/var/www`. |
| **web** | Nginx; document root `public`, proxies PHP to `app:9000`. |
| **db** | MySQL 8; database for the app. |
| **redis** | Redis 7; used as the queue driver (`QUEUE_CONNECTION=redis`). |
| **queue** | Same image as `app`; entrypoint waits for `vendor/autoload.php`, runs migrate, then `php artisan queue:work`. Uses Redis for jobs; no composer/npm; shares `.:/var/www` with app. |

## Where Policies Apply

- **Course**: `view` (guests can view published), `enroll` (authenticated, published only). Used on course show and enroll button.
- **Lesson**: `view` (guest + free preview, or enrolled). Used on lesson show.
- **Enrollment**: `view` (user can only see own). Used in Filament / future enrollment listing.

## Route Model Binding

- **Course**: Resolved by `slug`; `resolveRouteBinding` scopes to non–soft-deleted records and eager-loads `lessons`. Unique index on `slug` keeps the lookup fast.
- **Lesson**: Resolved by id; controller/component ensures the lesson belongs to the given course.

## Seeding

- **DatabaseSeeder** runs: 10 random users, a fixed **Test User** (`test@example.com`), **AdminSeeder** (Filament admin `admin@example.com`), and **CourseSeeder** (published courses + draft with lessons). Run `php artisan migrate --seed` for a full dev dataset.
