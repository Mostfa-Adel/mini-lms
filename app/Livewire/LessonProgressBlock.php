<?php

namespace App\Livewire;

use App\Actions\MarkLessonCompletedAction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class LessonProgressBlock extends Component
{
    public Course $course;

    public Lesson $lesson;

    public function mount(Course $course, Lesson $lesson): void
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }
        $this->course = $course;
        $this->lesson = $lesson;
    }

    public function markCompleted(): void
    {
        $this->authorize('view', $this->lesson);
        app(MarkLessonCompletedAction::class)->execute(Auth::user(), $this->lesson);
        $this->dispatch('lesson-completed');
    }

    #[On('mark-lesson-completed')]
    public function onMarkLessonCompleted(): void
    {
        $this->markCompleted();
    }

    public function getProgressProperty(): array
    {
        if (! Auth::check()) {
            return ['completed' => 0, 'total' => $this->course->lessons()->count(), 'percentage' => 0];
        }
        $total = $this->course->lessons()->count();
        $completed = \App\Models\LessonProgress::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->whereIn('lesson_id', $this->course->lessons()->pluck('id'))
            ->count();

        return [
            'completed' => $completed,
            'total' => $total,
            'percentage' => $total > 0 ? round((100 * $completed) / $total, 1) : 0,
        ];
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

    public function getIsCompletedProperty(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return \App\Models\LessonProgress::query()
            ->where('user_id', Auth::id())
            ->where('lesson_id', $this->lesson->id)
            ->whereNotNull('completed_at')
            ->exists();
    }

    public function render()
    {
        return view('livewire.lesson-progress-block');
    }
}
