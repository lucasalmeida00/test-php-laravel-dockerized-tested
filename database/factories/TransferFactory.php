<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfer>
 */
class TransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recipient_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 0, 10000),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(Transfer::TYPES),
            'status' => fake()->randomElement(Transfer::STATUSES),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
