<?php

namespace Database\Factories;

use App\Models\WorkProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkProgram>
 */
class WorkProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'planned_start_date' => fake()->dateTimeBetween('first day of January this year', 'last day of December this year')->format('Y-m-d'),
            'planned_end_date' => null,
            'actual_start_date' => null,
            'actual_end_date' => null,
            'status' => WorkProgram::STATUS_PLANNED,
            'created_by' => User::factory(),
        ];
    }
}
