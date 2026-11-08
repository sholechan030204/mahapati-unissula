<?php

namespace Database\Factories;

use App\Models\CashTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashTransaction>
 */
class CashTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'type' => fake()->randomElement([CashTransaction::TYPE_INCOME, CashTransaction::TYPE_EXPENSE]),
            'amount' => fake()->numberBetween(10000, 5000000),
            'description' => fake()->sentence(4),
        ];
    }
}
