<?php

namespace Database\Factories;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'slug' => fake()->unique()->slug(),
            'level' => fake()->randomElement(['Universitas', 'Regional', 'Nasional']),
            'achievement_date' => fake()->date(),
            'organizer' => fake()->company(),
            'summary' => fake()->sentence(18),
            'description' => fake()->paragraphs(3, true),
            'image_path' => null,
            'status' => Achievement::STATUS_DRAFT,
            'rejection_reason' => null,
            'created_by' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'published_at' => null,
        ];
    }
}
