<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Recipient;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gift>
 */
class GiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipient_id' => Recipient::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 500),
            'url' => fake()->url(),
            'purchased' => fake()->boolean(20), // 20% chance of being purchased
        ];
    }
}
