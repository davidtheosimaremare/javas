<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatisticSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('statistics')->truncate();

        $stats = [
            [
                'value' => 520,
                'label' => 'Proyek Terselesaikan',
                'order' => 1,
            ],
            [
                'value' => 720,
                'label' => 'Titik Distribusi di Seluruh Indonesia',
                'order' => 2,
            ],
            [
                'value' => 1000,
                'label' => 'Mitra Kerja Terpercaya',
                'order' => 3,
            ],
        ];

        foreach ($stats as $stat) {
            DB::table('statistics')->insert(array_merge($stat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}