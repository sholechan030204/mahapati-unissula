<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\WorkProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'work_program_id' => WorkProgram::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'planned_at' => fake()->dateTimeBetween('now', '+6 months'),
            'realized_at' => null,
            'status' => Activity::STATUS_PLANNED,
            'cover_image' => null,
            'is_featured' => false,
            'created_by' => User::factory(),
        ];
    }
}
