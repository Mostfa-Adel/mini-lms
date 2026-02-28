# Product Thinking

## 1. Business Risks

**1. Duplicate enrollments and inconsistent state**  
Rapid clicks or retries could create multiple enrollments or progress records.  
**Mitigation**: Unique DB constraints on `(user_id, course_id)` and `(user_id, lesson_id)`; enrollment and progress flows use transactions and idempotent actions (`firstOrCreate` / `updateOrCreate`). This keeps data consistent and prevents duplicate enrollments and progress.

**2. Certificates or completion emails sent more than once**  
Listeners might run multiple times (e.g. queue retries), leading to duplicate certificates or emails.  
**Mitigation**: Certificate is created with `firstOrCreate` on `(user_id, course_id)` so only one record exists. Completion email listener checks and sets `completion_email_sent` on the certificate and uses a transaction/lock so the email is sent only once.

**3. Abuse or cross-user access**  
Users might try to access another user’s enrollments, progress, or certificates.  
**Mitigation**: Policies enforce isolation (e.g. `EnrollmentPolicy::view` allows only the enrollment owner). Course and lesson views use the same policies so users only see and act on their own data.

---

## 2. Metrics That Matter

If working with Product, these five metrics would be useful:

1. **Enrollments per course**  
   **Where**: `enrollments` table (count by `course_id`).  
   **Compute vs store**: Either compute on demand (with index on `course_id`) or maintain a cached/aggregate value updated on enrollment.  
   **Performance**: Index on `course_id`; for high traffic, consider a materialized count or cache.

2. **Completion rate (per course)**  
   **Where**: `certificates` (or completed progress) vs enrollments.  
   **Compute**: Count certificates per course / count enrollments per course; can be computed in a scheduled job and stored in a stats table.  
   **Performance**: Avoid heavy ad-hoc aggregates on every request; use async aggregation or precomputed tables.

3. **Time to complete (course)**  
   **Where**: First enrollment date and certificate `issued_at` (or last lesson `completed_at`).  
   **Compute**: Stored or computed from `enrollments.created_at` and `certificates.issued_at`.  
   **Performance**: Store derived value when issuing certificate or run batch job.

4. **Certificate issuance volume**  
   **Where**: `certificates` table.  
   **Compute**: Count by day/course; store in analytics or query with indexes.  
   **Performance**: Index on `issued_at`, `course_id`; aggregate in background if needed.

5. **Email delivery (welcome / completion)**  
   **Where**: Queue jobs and mailer logs (or a small `email_sent` log table).  
   **Compute**: Track job success/failure; optional flag like `completion_email_sent` already exists.  
   **Performance**: Async sending (queued listeners); avoid blocking the request.

---

## 3. Future Evolution

**Paid courses**  
- **Supports**: Actions and policies can be extended; enrollment flow is already centralized.  
- **Refactor**: Add a pricing/payments layer (e.g. Stripe), an `order` or `purchase` model, and gate enrollment behind “paid or free” and access rules.

**Mobile app API**  
- **Supports**: Same actions (Enroll, MarkComplete, IssueCertificate) can be called from API controllers; events/listeners unchanged.  
- **Refactor**: Add API routes, API auth (e.g. Sanctum), and possibly API-specific validation and responses.

**Corporate multi-tenant accounts**  
- **Supports**: Event-driven design and actions make it easier to add tenant-aware logic.  
- **Refactor**: Introduce `tenant_id` (or similar) on courses/enrollments; scope queries and policies by tenant; ensure certificates and emails are tenant-aware.

**Gamification (e.g. badges)**  
- **Supports**: Events (e.g. CourseCompleted) can drive new listeners that award badges.  
- **Refactor**: Add a `badges` (or similar) model and a listener that runs when completion or other events fire; keep badge logic out of core enrollment/completion actions.

---

## 4. Trade-offs

**1. “Required” = all lessons in the course**  
We treat completion as “all current lessons completed” rather than a subset (e.g. “required” flag per lesson).  
**Why**: Simpler rules and implementation; course authors can control what counts by adding/removing lessons. A future “required” flag per lesson would require schema and logic changes.

**2. No guest progress**  
Guests can watch free preview lessons but we don’t persist progress for them.  
**Why**: Keeps the model simple (progress tied to enrollment/user) and avoids anonymous or session-based progress. Revisit if “resume as guest” becomes a product requirement.

**3. Synchronous completion check and certificate creation**  
Completion is detected and the certificate is created in the same request (via a sync listener).  
**Why**: Ensures the certificate exists before the completion email job runs and keeps the flow easy to reason about. Moving certificate creation to a queued job would require careful ordering (e.g. certificate job before email job) and idempotency.

**4. User registration without email verification**  
Users can register and use the app immediately; we do not require a verified email before they can enroll or access content.  
**Why**: Reduces friction for sign-up and keeps the flow simple. A welcome email is sent on registration but verification is not enforced. If you need verified-only access later, add `email_verified_at` checks and a verification flow (e.g. Laravel’s `MustVerifyEmail`).
