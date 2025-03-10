<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Enums\RolesEnum;
use App\Enums\VendorStatusEnum;
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
        
        $user = User::factory()->create([
            'name' => 'Vendor',
            'email' => 'vendor@example.com',
            'password'=> bcrypt('try'),
        ]);
        $user->assignRole(RolesEnum::Vendor->value); //
        Vendor::factory()->create([
            'user_id' => $user->id,
            'status' => VendorStatusEnum::Approved,
            'store_name' => 'Vendor Store',
            'store_address' => fake()->address(),
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password'=> bcrypt('try'),
        ])->assignRole(RolesEnum::Admin->value);
    }
}
