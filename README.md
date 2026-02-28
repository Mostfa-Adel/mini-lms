# Mini-LMS (Career 180)

A scalable Learning Management System built with Laravel 12, Livewire v3, Alpine.js, and Filament v3.

## Requirements

- Docker and Docker Compose
- (Optional for local dev without Docker) PHP 8.2+, Composer, Node/NPM, MySQL or SQLite

## Quick Start with Docker (development)

The stack is set up for **development**: code is mounted as a volume. The **app** container’s entrypoint runs `composer install`, `php artisan migrate` (with retry until DB is ready), and `npm ci && npm run build` on startup. The **queue** container waits for `vendor/` to exist (so app has run composer), then runs migrate and starts the worker. Dependencies are not installed at image build time.

```bash
cd mini-lms
cp .env.example .env
# Run as your user so vendor/storage stay owned by you (avoids permission and tempnam issues)
export HOST_UID=$(id -u) HOST_GID=$(id -g)
docker compose up --build
```

Or set `HOST_UID` and `HOST_GID` in `.env` (default in compose is `1001`; use `id -u` / `id -g` to match your user). The app and queue containers run as this user so the project owner matches the host.

Docker overrides `DB_*` to use the MySQL container, so you can keep `DB_CONNECTION=sqlite` in `.env` for local use without Docker.

**After first start** (wait for the app container to finish `composer install` and `npm run build`), run in another terminal:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan test
```

If you skip `migrate --seed`, the app will return 500 (missing database tables). Seeding creates 10 users, Test User (`test@example.com`), Filament admin (`admin@example.com` / password), and sample published courses with lessons.

- **App**: http://localhost:8000  
- **Admin (Filament)**: http://localhost:8000/admin (login: admin@example.com / password)  
- **Queue**: runs in the `queue` container (Redis-backed; waits for app’s vendor then starts worker).

## Bootstrap Summary

| Step              | Command (inside `app` container)        |
|-------------------|----------------------------------------|
| Install deps     | Automatic on container start (entrypoint) |
| Migrations + seed| `php artisan migrate --seed`           |
| Queue worker     | Handled by `queue` service in Docker   |
| Tests            | `php artisan test`                     |

## Assumptions

- **Required lessons**: A course is considered complete when the user has completed **all** lessons in that course (at the time of completion). If lessons are added or removed later, completion is based on the current lesson set.
- **Slug uniqueness**: Course slugs are unique (including soft-deleted courses; reusing a slug after delete would require application-level handling). Lesson slugs are unique per course.
- **Free preview**: Guests can view lessons marked as free preview without enrolling.

## Tech Stack

- Laravel 12, Livewire v3, Alpine.js, Filament v3, Pest, MySQL, Plyr.js, Tailwind, Vite
- Docker (dev): `app` (PHP 8.3-FPM, intl, Redis ext.), `web` (Nginx), `db` (MySQL 8), `redis`, `queue` (Redis-backed worker). Composer/npm run at container start via entrypoint, not at image build.

## Project Structure

- **Actions**: `app/Actions/` — EnrollUserInCourse, MarkLessonCompleted, IssueCertificate
- **Events / Listeners**: User registration (welcome email), Course completed (certificate + completion email)
- **Policies**: Course, Lesson, Enrollment
- **Docs**: `docs/ARCHITECTURE.md`, `docs/PRODUCT_THINKING.md`, `docs/ERD.md`
<img width="684" height="417" alt="image" src="https://github.com/user-attachments/assets/082a7335-9fcb-4685-93f2-c08bab172944" />

