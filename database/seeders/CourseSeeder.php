<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Published course 1
        $course1 = Course::create([
            'title' => 'Laravel Fundamentals',
            'slug' => 'laravel-fundamentals',
            'level' => 'beginner',
            'description' => 'Learn the basics of Laravel framework.',
            'is_published' => true,
        ]);
        $this->createLessonsForCourse($course1, [
            ['title' => 'Introduction', 'free' => true],
            ['title' => 'Routing', 'free' => false],
            ['title' => 'Controllers', 'free' => false],
        ]);

        // Published course 2
        $course2 = Course::create([
            'title' => 'Livewire & Alpine',
            'slug' => 'livewire-alpine',
            'level' => 'intermediate',
            'description' => 'Build reactive UIs with Livewire and Alpine.js.',
            'is_published' => true,
        ]);
        $this->createLessonsForCourse($course2, [
            ['title' => 'Getting Started', 'free' => true],
            ['title' => 'Components', 'free' => false],
            ['title' => 'Forms', 'free' => false],
            ['title' => 'Alpine Integration', 'free' => false],
        ]);

        // Published course 3
        $course3 = Course::create([
            'title' => 'PHP Testing with Pest',
            'slug' => 'php-testing-pest',
            'level' => 'intermediate',
            'description' => 'Write elegant tests with Pest.',
            'is_published' => true,
        ]);
        $this->createLessonsForCourse($course3, [
            ['title' => 'Pest Basics', 'free' => true],
            ['title' => 'Expectations', 'free' => false],
        ]);

        // Draft course (for testing "no enrollment for draft")
        $draft = Course::create([
            'title' => 'Draft Course (Unpublished)',
            'slug' => 'draft-course',
            'level' => 'beginner',
            'description' => 'This course is not published.',
            'is_published' => false,
        ]);
        $this->createLessonsForCourse($draft, [
            ['title' => 'Draft Lesson', 'free' => false],
        ]);
    }

    private function createLessonsForCourse(Course $course, array $lessons): void
    {
        foreach ($lessons as $index => $item) {
            Lesson::create([
                'course_id' => $course->id,
                'title' => $item['title'],
                'slug' => Str::slug($item['title']) . '-' . ($index + 1),
                'sort_order' => $index + 1,
                'video_url' => 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-720p.mp4',
                'is_free_preview' => $item['free'],
            ]);
        }
    }
}
