<?php

namespace Database\Factories;

use App\Models\AccountRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AccountRequest>
 */
class AccountRequestFactory extends Factory
{
    protected $model = AccountRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'nra' => strtoupper(fake()->bothify('MP-####')),
            'nim' => fake()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'whatsapp' => '08'.fake()->numerify('##########'),
            'status' => AccountRequest::STATUS_PENDING,
            'rejection_reason' => null,
            'reviewed_by' => null,
            'approved_user_id' => null,
            'reviewed_at' => null,
        ];
    }

    public function rejected(): static
    {
        return $this->state(fn (): array => [
            'status' => AccountRequest::STATUS_REJECTED,
            'rejection_reason' => fake()->sentence(),
            'reviewed_at' => now(),
        ]);
    }
}
