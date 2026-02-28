<?php

namespace App\Actions;

use App\Events\CourseCompleted;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MarkLessonCompletedAction
{
    /**
     * Mark a lesson as completed for the user. Idempotent.
     * Dispatches CourseCompleted when all required lessons are done (listeners issue certificate + send email once).
     *
     * @throws InvalidArgumentException When user is not enrolled or lesson does not belong to course.
     */
    public function execute(User $user, Lesson $lesson): LessonProgress
    {
        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->first();

        if (! $enrollment) {
            throw new InvalidArgumentException('User is not enrolled in this course.');
        }

        return DB::transaction(function () use ($user, $lesson, $enrollment) {
            $progress = LessonProgress::query()
                ->where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->first();

            $now = now();
            if ($progress) {
                if (! $progress->started_at) {
                    $progress->update(['started_at' => $now]);
                }
                $progress->update(['completed_at' => $now]);
            } else {
                $progress = LessonProgress::create([
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                    'enrollment_id' => $enrollment->id,
                    'started_at' => $now,
                    'completed_at' => $now,
                ]);
            }

            $this->checkAndDispatchCourseCompleted($user, $lesson->course);

            return $progress->fresh();
        });
    }

    private function checkAndDispatchCourseCompleted(User $user, \App\Models\Course $course): void
    {
        $requiredLessons = $course->lessons()->count();
        if ($requiredLessons === 0) {
            return;
        }

        $completedCount = LessonProgress::query()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->count();

        if ($completedCount >= $requiredLessons) {
            event(new CourseCompleted($user->id, $course->id));
        }
    }
}
