<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::table('users')->insert([
            'name' => 'mitarbeiter',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('admin'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'profile_picture' => fake()->imageUrl(),
            'status' => 'active',
        ]);
        DB::table('admins')->insert([
            'name' => 'admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('admin'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'profile_picture' => fake()->imageUrl(),
            'status' => 'active',
        ]);
        DB::table('Azubis')->insert([
            'name' => 'azubi',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('azubi'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'profile_picture' => fake()->imageUrl(),
            'status' => 'active',
        ]);
    }
}
