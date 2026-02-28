<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class CourseCompleted
{
    use Dispatchable;

    public function __construct(
        public int $userId,
        public int $courseId
    ) {}
}
