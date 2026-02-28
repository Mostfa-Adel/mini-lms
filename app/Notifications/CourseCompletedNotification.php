<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $courseTitle,
        public string $certificateUuid
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Course Completed: {$this->courseTitle}")
            ->line("Congratulations {$notifiable->name}! You have completed the course: {$this->courseTitle}.")
            ->line("Your certificate UUID: {$this->certificateUuid}");
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
