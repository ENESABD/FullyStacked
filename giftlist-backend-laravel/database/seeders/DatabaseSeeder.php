<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Recipient;
use App\Models\Gift;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a specific Test User for login testing
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123', // Password will be hashed by the model mutator/factory
        ]);

        // 2. Add 5 Recipients to the Test User
        Recipient::factory(5)
            ->for($testUser)
            ->has(Gift::factory()->count(3)) // Each recipient gets 3 gifts
            ->create();

        // 3. Create 5 other random users with data to test isolation
        User::factory(5)
            ->has(
                Recipient::factory()
                    ->count(3)
                    ->has(Gift::factory()->count(2))
            )
            ->create();
    }
}
