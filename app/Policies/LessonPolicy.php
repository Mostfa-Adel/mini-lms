<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class LessonPolicy
{
    /**
     * Web: guests see free preview only; users must be enrolled. Admin panel: Admin can view any.
     */
    public function view(?Authenticatable $user, Lesson $lesson): bool
    {
        if ($lesson->is_free_preview) {
            return true;
        }
        if (! $user) {
            return false;
        }
        if ($user instanceof Admin) {
            return true;
        }

        /** @var User $user */
        return $user->enrollments()
            ->where('course_id', $lesson->course_id)
            ->exists();
    }

    /** Admin panel (Filament) – Admin model only. */
    public function viewAny(Admin $admin): bool
    {
        return true;
    }

    public function create(Admin $admin): bool
    {
        return true;
    }

    public function update(Admin $admin, Lesson $lesson): bool
    {
        return true;
    }

    public function delete(Admin $admin, Lesson $lesson): bool
    {
        return true;
    }

    public function restore(Admin $admin, Lesson $lesson): bool
    {
        return true;
    }

    public function forceDelete(Admin $admin, Lesson $lesson): bool
    {
        return true;
    }
}
