<?php

namespace App\Livewire;

use App\Services\CourseListingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ExploreCourses extends Component
{
    public function render(CourseListingService $courseListing)
    {
        $userId = Auth::id();

        return view('explore-courses', [
            'courses' => $courseListing->exploreCourses(12, $userId),
        ])->layout('layouts.app');
    }
}
