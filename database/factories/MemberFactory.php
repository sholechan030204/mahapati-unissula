<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'alam_name' => fake()->optional()->firstName(),
            'nra' => strtoupper(fake()->unique()->bothify('MP-####')),
            'nim' => fake()->optional()->numerify('##########'),
            'membership_type' => fake()->randomElement([Member::TYPE_PERMANENT, Member::TYPE_SPECIAL]),
            'status' => fake()->randomElement([Member::STATUS_ACTIVE, Member::STATUS_INACTIVE]),
            'position' => fake()->optional()->jobTitle(),
            'profile_photo' => 'member-photos/example-profile.jpg',
            'address' => fake()->address(),
            'phone' => '08'.fake()->numerify('##########'),
        ];
    }
}
