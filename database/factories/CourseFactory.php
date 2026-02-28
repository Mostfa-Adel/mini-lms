<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->randomNumber(4),
            'image' => null,
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'description' => fake()->paragraphs(2, true),
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => false]);
    }
}
