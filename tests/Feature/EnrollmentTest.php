<?php

use App\Actions\EnrollUserInCourseAction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = app(EnrollUserInCourseAction::class);
});

test('enrollment creates one record', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);

    $enrollment1 = $this->action->execute($user, $course);
    $enrollment2 = $this->action->execute($user, $course);

    expect($enrollment1->id)->toBe($enrollment2->id);
    expect(Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->count())->toBe(1);
});

test('enrollment in draft course fails', function () {
    $user = User::factory()->create();
    $course = Course::factory()->draft()->create();

    $this->action->execute($user, $course);
})->throws(InvalidArgumentException::class, 'Cannot enroll in an unpublished course');

test('enrollment creates enrollment record for user and course', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);

    $this->action->execute($user, $course);

    expect(Enrollment::count())->toBe(1);
    expect(Enrollment::first()->user_id)->toBe($user->id);
    expect(Enrollment::first()->course_id)->toBe($course->id);
});

test('duplicate enrollment is idempotent', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);

    $e1 = $this->action->execute($user, $course);
    $e2 = $this->action->execute($user, $course);

    expect(Enrollment::count())->toBe(1);
    expect($e1->id)->toBe($e2->id);
});
