# Entity Relationship Diagram

```mermaid
erDiagram
    users ||--o{ enrollments : has
    users ||--o{ lesson_progress : has
    users ||--o{ certificates : has

    courses ||--o{ lessons : contains
    courses ||--o{ enrollments : has
    courses ||--o{ certificates : has

    lessons ||--o{ lesson_progress : has

    enrollments ||--o{ lesson_progress : has

    users {
        bigint id PK
        string name
        string email
        string password
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }

    courses {
        bigint id PK
        string title
        string slug UK
        string image
        string level
        text description
        boolean is_published
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }

    lessons {
        bigint id PK
        bigint course_id FK
        string title
        string slug
        int sort_order
        string video_url
        boolean is_free_preview
        timestamp created_at
        timestamp updated_at
    }

    enrollments {
        bigint id PK
        bigint user_id FK
        bigint course_id FK
        timestamp created_at
        timestamp updated_at
    }

    lesson_progress {
        bigint id PK
        bigint user_id FK
        bigint lesson_id FK
        bigint enrollment_id FK
        timestamp started_at
        timestamp completed_at
        timestamp created_at
        timestamp updated_at
    }

    certificates {
        bigint id PK
        uuid uuid UK
        bigint user_id FK
        bigint course_id FK
        timestamp issued_at
        boolean completion_email_sent
        timestamp created_at
        timestamp updated_at
    }
```

## Key constraints

- **courses**: `slug` unique (soft deletes: row remains so slug is still reserved).
- **lessons**: `(course_id, slug)` unique.
- **enrollments**: `(user_id, course_id)` unique.
- **lesson_progress**: `(user_id, lesson_id)` unique.
- **certificates**: `uuid` unique, `(user_id, course_id)` unique.
