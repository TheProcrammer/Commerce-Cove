<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating accounts with different Roles to check if the change was applied.
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password'=> bcrypt('try'),
        ])->assignRole(RolesEnum::User);

        User::factory()->create([
            'name' => 'Vendor',
            'email' => 'vendor@example.com',
            'password'=> bcrypt('try'),
        ])->assignRole(RolesEnum::Vendor);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password'=> bcrypt('try'),
        ])->assignRole(RolesEnum::Admin->value);
    }
}
