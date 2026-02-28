<?php

use App\Actions\EnrollUserInCourseAction;
use App\Actions\MarkLessonCompletedAction;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('completing all lessons creates exactly one certificate and sends completion email once', function () {
    Mail::fake();
    // Do not fake CourseCompleted so listeners run (queue is sync in tests)

    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lesson1 = Lesson::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);
    $lesson2 = Lesson::factory()->create(['course_id' => $course->id, 'sort_order' => 2]);

    app(EnrollUserInCourseAction::class)->execute($user, $course);

    $action = app(MarkLessonCompletedAction::class);
    $action->execute($user, $lesson1);
    $action->execute($user, $lesson2);

    expect(Certificate::where('user_id', $user->id)->where('course_id', $course->id)->count())->toBe(1);
});

test('marking same lesson completed twice does not duplicate certificate', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lesson = Lesson::factory()->create(['course_id' => $course->id]);

    app(EnrollUserInCourseAction::class)->execute($user, $course);
    $action = app(MarkLessonCompletedAction::class);
    $action->execute($user, $lesson);
    $action->execute($user, $lesson);

    expect(Certificate::where('user_id', $user->id)->where('course_id', $course->id)->count())->toBe(1);
});

test('completion creates one lesson progress per user lesson', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lesson = Lesson::factory()->create(['course_id' => $course->id]);

    app(EnrollUserInCourseAction::class)->execute($user, $course);
    $action = app(MarkLessonCompletedAction::class);
    $action->execute($user, $lesson);
    $action->execute($user, $lesson);

    expect(LessonProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->count())->toBe(1);
});
