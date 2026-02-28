<?php

namespace App\Actions;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class IssueCertificateAction
{
    /**
     * Issue a certificate for a user and course. Idempotent: returns existing certificate if already issued.
     * Sets enrollment.completed_at so we have a denormalized "course completed" flag.
     */
    public function execute(User $user, Course $course): Certificate
    {
        $certificate = Certificate::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'issued_at' => now(),
            ]
        );

        Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereNull('completed_at')
            ->update(['completed_at' => $certificate->issued_at]);

        return $certificate;
    }
}
