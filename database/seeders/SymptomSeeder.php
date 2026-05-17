<?php

namespace Database\Seeders;

use App\Models\Symptom;
use Illuminate\Database\Seeder;

class SymptomSeeder extends Seeder
{
    public function run(): void
    {
        $symptoms = [
            // [name, category, weight, is_critical]
            ['Mual',             'fisik',      10, false],
            ['Muntah',           'fisik',      15, false],
            ['Pusing',           'fisik',      15, false],
            ['Sakit Kepala',     'fisik',      20, false],
            ['Nyeri Perut',      'fisik',      30, true],
            ['Pendarahan',       'fisik',      50, true],
            ['Sesak Nafas',      'fisik',      35, true],
            ['Bengkak Tangan',   'fisik',      25, false],
            ['Kontraksi',        'fisik',      40, true],
            ['Demam',            'fisik',      20, false],
            ['Cemas Berlebihan', 'emosional',  20, false],
            ['Panik',            'emosional',  35, true],
            ['Depresi',          'emosional',  30, true],
            ['Insomnia',         'emosional',  10, false],
        ];

        foreach ($symptoms as [$name, $category, $weight, $critical]) {
            Symptom::create([
                'name'        => $name,
                'category'    => $category,
                'weight'      => $weight,
                'is_critical' => $critical,
            ]);
        }
    }
}