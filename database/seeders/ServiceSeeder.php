<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama
        DB::table('services')->truncate();

        $services = [
            [
                'title' => 'Layanan EPC Power',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
                'image' => '/storage/services/service-1.jpg', 
                'color' => '#00529c', // Biru
                'link' => '#',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Distribusi Energi',
                'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.',
                'image' => '/storage/services/service-2.jpg',
                'color' => '#dc3545', // Merah
                'link' => '#',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Solusi Aviasi',
                'description' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore.',
                'image' => '/storage/services/service-3.jpg',
                'color' => '#198754', // Hijau
                'link' => '#',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Konsultasi Teknik',
                'description' => 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.',
                'image' => '/storage/services/service-4.jpg',
                'color' => '#fd7e14', // Orange
                'link' => '#',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($services as $data) {
            DB::table('services')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}