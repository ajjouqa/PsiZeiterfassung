<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email'=> fake()->unique()->safeEmail(),
            'password' => bcrypt('admin'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'profile_picture' => fake()->imageUrl(),
            'status' => 'active',
        ]);

    }
}
