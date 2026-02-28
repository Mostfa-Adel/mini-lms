<?php

use App\Actions\EnrollUserInCourseAction;
use App\Actions\MarkLessonCompletedAction;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('course with zero lessons does not dispatch course completed', function () {
    Event::fake([\App\Events\CourseCompleted::class]);

    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    // No lessons

    app(EnrollUserInCourseAction::class)->execute($user, $course);
    // Mark completed is not called for any lesson - so no event.
    Event::assertNotDispatched(\App\Events\CourseCompleted::class);
});

test('completing all current lessons creates certificate once', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lesson = Lesson::factory()->create(['course_id' => $course->id]);

    app(EnrollUserInCourseAction::class)->execute($user, $course);
    app(MarkLessonCompletedAction::class)->execute($user, $lesson);

    expect(Certificate::where('user_id', $user->id)->where('course_id', $course->id)->count())->toBe(1);
});

test('user not enrolled cannot mark lesson completed', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lesson = Lesson::factory()->create(['course_id' => $course->id]);
    // User not enrolled

    app(MarkLessonCompletedAction::class)->execute($user, $lesson);
})->throws(InvalidArgumentException::class, 'User is not enrolled');
