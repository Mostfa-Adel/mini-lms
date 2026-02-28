<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);
        return [
            'course_id' => Course::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->randomNumber(3),
            'sort_order' => 0,
            'video_url' => 'https://example.com/video.mp4',
            'is_free_preview' => false,
        ];
    }

    public function freePreview(): static
    {
        return $this->state(fn (array $attributes) => ['is_free_preview' => true]);
    }
}
