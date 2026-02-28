<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseListingService
{
    /**
     * Courses available to explore (published, excluding any the user is enrolled in).
     */
    public function exploreCourses(int $perPage = 12, ?int $userId = null): LengthAwarePaginator
    {
        $query = Course::published();

        if ($userId !== null) {
            $query->whereDoesntHave('enrollments', fn ($q) => $q->where('user_id', $userId));
        }

        return $query->paginate($perPage);
    }

    /**
     * Courses the user is enrolled in (published only).
     */
    public function myLearningCourses(int $userId, int $perPage = 12): LengthAwarePaginator
    {
        return Course::published()
            ->whereHas('enrollments', fn ($q) => $q->where('user_id', $userId))
            ->paginate($perPage);
    }
}
