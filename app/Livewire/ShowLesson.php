<?php

namespace App\Livewire;

use App\Actions\RecordLessonStartedAction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ShowLesson extends Component
{
    public Course $course;

    public Lesson $lesson;

    public function mount(Course $course, Lesson $lesson): void
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }
        $this->authorize('view', $lesson);
        $this->course = $course;
        $this->lesson = $lesson;

        // Record "lesson started" on first view (started_at only; completed_at set when they mark complete)
        if (Auth::check()) {
            $enrollment = Enrollment::query()
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->exists();
            if ($enrollment) {
                app(RecordLessonStartedAction::class)->execute(Auth::user(), $lesson);
            }
        }
    }

    public function getIsEnrolledProperty(): bool
    {
        if (! Auth::check()) {
            return false;
        }
        return Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->exists();
    }

    public function getNavigableLessonsProperty(): \Illuminate\Support\Collection
    {
        $query = $this->course->lessons()->ordered();

        if (! $this->isEnrolled) {
            $query->where('is_free_preview', true);
        }

        return $query->get();
    }

    public function getPreviousLessonProperty(): ?Lesson
    {
        $lessons = $this->navigableLessons;
        $idx = $lessons->search(fn (Lesson $l) => $l->id === $this->lesson->id);
        if ($idx === false || $idx < 1) {
            return null;
        }
        return $lessons->get($idx - 1);
    }

    public function getNextLessonProperty(): ?Lesson
    {
        $lessons = $this->navigableLessons;
        $idx = $lessons->search(fn (Lesson $l) => $l->id === $this->lesson->id);
        if ($idx === false || $idx >= $lessons->count() - 1) {
            return null;
        }
        return $lessons->get($idx + 1);
    }

    public function render()
    {
        return view('livewire.show-lesson')->layout('layouts.app');
    }
}
