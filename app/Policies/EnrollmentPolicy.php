<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Web: user sees own enrollment only. Admin panel: Admin sees any.
     */
    public function view(User|Admin $user, Enrollment $enrollment): bool
    {
        return $user instanceof Admin || $enrollment->user_id === $user->id;
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

    public function update(Admin $admin, Enrollment $enrollment): bool
    {
        return true;
    }

    public function delete(Admin $admin, Enrollment $enrollment): bool
    {
        return true;
    }

    public function restore(Admin $admin, Enrollment $enrollment): bool
    {
        return true;
    }

    public function forceDelete(Admin $admin, Enrollment $enrollment): bool
    {
        return true;
    }
}
