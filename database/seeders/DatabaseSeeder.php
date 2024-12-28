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
        // Centralizing seeds. It calls all the Seeders declared here.
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
