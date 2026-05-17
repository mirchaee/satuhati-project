<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SymptomSeeder::class,
            EducationalContentSeeder::class,
            UserSeeder::class,          // Terakhir karena butuh tabel lain
        ]);
    }
}