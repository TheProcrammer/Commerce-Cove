<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "name" => "Electronics",
                "department_id" => 1, // Matches Electronics
                "parent_id" => null, // Root category
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Home Appliances",
                "department_id" => 2, // Matches Electronics
                "parent_id" => null, // Root category
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Computers",
                "department_id" => 1, // Matches Electronics
                "parent_id" => 1, // Root category
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Laptops",
                "department_id" => 1, // Matches Electronics
                "parent_id" => 1, // Root category
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Vacuum Cleaners",
                "department_id" => 2, // Matches Home Appliances
                "parent_id" => 2, // Root category
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
        ];                       
        DB::table('categories')->insert($categories); 
    }
}
