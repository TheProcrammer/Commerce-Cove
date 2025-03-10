<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Enums\VendorStatusEnum;
use App\Enums\RolesEnum;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $user = User::factory()->create([
                'name' => 'Vendor 3',
                'email' => 'vendor3@example.com',
                'password'=> bcrypt('try'),
            ]);
            $user->assignRole(RolesEnum::Vendor->value);

            Vendor::factory()->create([
                'user_id' => $user->id,
                'status' => VendorStatusEnum::Approved,
                'store_name' => fake()->company,
                'store_address' => fake()->address,
            ]);
    }
}
