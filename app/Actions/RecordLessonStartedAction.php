<?php

namespace App\Actions;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use InvalidArgumentException;

class RecordLessonStartedAction
{
    /**
     * Record that the user has started viewing this lesson (first review).
     * Sets started_at only; completed_at is set when they explicitly mark the lesson completed.
     * Idempotent: safe to call on every lesson page view.
     *
     * @throws InvalidArgumentException When user is not enrolled or lesson does not belong to course.
     */
    public function execute(User $user, Lesson $lesson): void
    {
        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->first();

        if (! $enrollment) {
            throw new InvalidArgumentException('User is not enrolled in this course.');
        }

        $progress = LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($progress) {
            if ($progress->started_at === null) {
                $progress->update(['started_at' => now()]);
            }
            return;
        }

        LessonProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'enrollment_id' => $enrollment->id,
            'started_at' => now(),
            'completed_at' => null,
        ]);
    }
}
