<?php

namespace App\Livewire;

use App\Services\CourseListingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyLearning extends Component
{
    public function render(CourseListingService $courseListing)
    {
        $courses = $courseListing->myLearningCourses(Auth::id(), 12);

        return view('my-learning', [
            'courses' => $courses,
        ])->layout('layouts.app');
    }
}
