<?php

namespace Database\Seeders;
;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $department = [
            [
                "name" => "Electronics",
                "slug" => Str::slug("electronics"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Home Appliances",
                "slug" => Str::slug("home-appliances"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Fashion",
                "slug" => Str::slug("fashion"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Books",
                "slug" => Str::slug("books"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Toys",
                "slug" => Str::slug("toys"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Sports",
                "slug" => Str::slug("sports"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Beauty",
                "slug" => Str::slug("beauty"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Automotive",
                "slug" => Str::slug("automotive"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Groceries",
                "slug" => Str::slug("groceries"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Furniture",
                "slug" => Str::slug("furniture"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ]
            
        ];
        DB::table('departments')->insert($department);
    }
}
