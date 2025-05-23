<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Generate API keys for all users
        \App\Models\User::all()->each(function($user) {
            if (!$user->api_key) {
                $user->api_key = bin2hex(random_bytes(32));
                $user->save();
            }
        });

        // Optionally, create a test user
        \App\Models\User::factory()->create([
            'username' => 'testuser', // <-- add this line
            'name' => 'Test User',
            'email' => 'test@example.com',
            'api_key' => bin2hex(random_bytes(32)),
        ]);
    }
}
