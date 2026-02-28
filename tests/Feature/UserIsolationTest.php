<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can only view own enrollments via policy', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $enrollmentB = Enrollment::factory()->create(['user_id' => $userB->id, 'course_id' => $course->id]);

    $this->actingAs($userA);
    expect($userA->can('view', $enrollmentB))->toBeFalse();

    $this->actingAs($userB);
    expect($userB->can('view', $enrollmentB))->toBeTrue();
});

test('guest can view published course', function () {
    $course = Course::factory()->create(['is_published' => true]);
    $policy = app(\App\Policies\CoursePolicy::class);
    expect($policy->view(null, $course))->toBeTrue();
});

