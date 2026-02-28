<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class CoursePolicy
{
    /**
     * Web: guests/users see published courses. Admin panel: Admin can view any.
     */
    public function view(?Authenticatable $user, Course $course): bool
    {
        return $course->is_published;
    }

    /**
     * Only app users can enroll in published courses. (Web app only.)
     */
    public function enroll(User $user, Course $course): bool
    {
        return $course->is_published;
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

    public function update(Admin $admin, Course $course): bool
    {
        return true;
    }

    public function delete(Admin $admin, Course $course): bool
    {
        return true;
    }

    public function restore(Admin $admin, Course $course): bool
    {
        return true;
    }

    public function forceDelete(Admin $admin, Course $course): bool
    {
        return true;
    }
}
