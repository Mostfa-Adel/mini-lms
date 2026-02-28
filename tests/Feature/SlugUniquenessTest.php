<?php

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('course slug must be unique', function () {
    Course::factory()->create(['slug' => 'same-slug']);

    expect(fn () => Course::factory()->create(['slug' => 'same-slug']))->toThrow(\Illuminate\Database\QueryException::class);
});

test('lesson slug must be unique per course', function () {
    $course = Course::factory()->create();
    Lesson::factory()->create(['course_id' => $course->id, 'slug' => 'intro']);

    expect(fn () => Lesson::factory()->create(['course_id' => $course->id, 'slug' => 'intro']))->toThrow(\Illuminate\Database\QueryException::class);
});

test('different courses can have lessons with same slug', function () {
    $course1 = Course::factory()->create();
    $course2 = Course::factory()->create();
    Lesson::factory()->create(['course_id' => $course1->id, 'slug' => 'intro']);
    Lesson::factory()->create(['course_id' => $course2->id, 'slug' => 'intro']);

    expect(Lesson::where('slug', 'intro')->count())->toBe(2);
});
