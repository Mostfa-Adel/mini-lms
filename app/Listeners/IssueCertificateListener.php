<?php

namespace App\Listeners;

use App\Actions\IssueCertificateAction;
use App\Events\CourseCompleted;
use App\Models\Course;
use App\Models\User;

class IssueCertificateListener
{
    public function __construct(
        private IssueCertificateAction $issueCertificateAction
    ) {}

    public function handle(CourseCompleted $event): void
    {
        $user = User::findOrFail($event->userId);
        $course = Course::findOrFail($event->courseId);
        $this->issueCertificateAction->execute($user, $course);
    }
}
