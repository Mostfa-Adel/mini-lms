<?php

namespace App\Livewire;

use App\Actions\EnrollUserInCourseAction;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ShowCourse extends Component
{
    public Course $course;

    public function mount(Course $course): void
    {
        $this->authorize('view', $course);
        $this->course = $course->load('lessons');
    }

    public function enroll(): void
    {
        $this->authorize('enroll', $this->course);
        app(EnrollUserInCourseAction::class)->execute(Auth::user(), $this->course);
        $this->dispatch('enrolled');
    }

    public function getIsEnrolledProperty(): bool
    {
        return Auth::check() && $this->course->enrollments()
            ->where('user_id', Auth::id())->exists();
    }

    public function render()
    {
        return view('livewire.show-course')->layout('layouts.app');
    }
}
