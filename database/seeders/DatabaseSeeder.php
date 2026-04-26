<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create random users
        User::factory(5)->hasTasks(3)->create();

        // Create the test user
        $user = User::query()->updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
        ]);

        // Create tasks and comments for the test user
        \App\Models\Task::factory(5)
            ->for($user)
            ->hasComments(2, function (array $attributes, \App\Models\Task $task) use ($user) {
                // Ensure comments belong to the same user or a random one
                return ['user_id' => $user->id];
            })
            ->create();
    }
}
