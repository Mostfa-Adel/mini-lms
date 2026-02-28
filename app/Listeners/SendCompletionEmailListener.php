<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Notifications\CourseCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class SendCompletionEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CourseCompleted $event): void
    {
        DB::transaction(function () use ($event) {
            $certificate = Certificate::query()
                ->where('user_id', $event->userId)
                ->where('course_id', $event->courseId)
                ->lockForUpdate()
                ->first();

            if (! $certificate || $certificate->completion_email_sent) {
                return;
            }
            $user = User::findOrFail($event->userId, ['id', 'name', 'email']);
            $course = Course::findOrFail($event->courseId, ['id', 'title']);
            $user->notify(new CourseCompletedNotification($course->title, $certificate->uuid));
            $certificate->update(['completion_email_sent' => true]);
        });
    }
}
