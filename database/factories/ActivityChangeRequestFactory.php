<?php

namespace Database\Factories;

use App\Models\ActivityChangeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityChangeRequest>
 */
class ActivityChangeRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_type' => ActivityChangeRequest::TYPE_PROGRAM,
            'subject_id' => null,
            'action' => ActivityChangeRequest::ACTION_CREATE,
            'payload' => [],
            'note' => fake()->optional()->sentence(),
            'status' => ActivityChangeRequest::STATUS_PENDING,
            'rejection_reason' => null,
            'proposed_by' => User::factory(),
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }
}
