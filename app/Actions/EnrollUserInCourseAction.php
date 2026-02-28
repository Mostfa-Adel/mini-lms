<?php

namespace App\Actions;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EnrollUserInCourseAction
{
    /**
     * Enroll a user in a course. Idempotent: returns existing enrollment if already enrolled.
     *
     * @throws InvalidArgumentException When user is guest, course is draft, or validation fails.
     */
    public function execute(User $user, Course $course): Enrollment
    {
        if (! $course->is_published) {
            throw new InvalidArgumentException('Cannot enroll in an unpublished course.');
        }

        return DB::transaction(function () use ($user, $course) {
            $enrollment = Enrollment::query()
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->lockForUpdate()
                ->first();

            if ($enrollment) {
                return $enrollment;
            }

            return Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
        });
    }
}
