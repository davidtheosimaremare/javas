<?php

namespace Database\Seeders;

// File: database/seeders/GalleryItemSeeder.php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GalleryItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('gallery_items')->truncate();
        
        // Memasukkan lebih dari 1 data gambar
        DB::table('gallery_items')->insert([
            [
                'image_url' => 'https://example.com/images/project1-gi.jpg', 
                'caption' => 'Pemasangan Gardu Induk Sisi Utara', 
                'order' => 1,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'image_url' => 'https://example.com/images/project2-team.jpg', 
                'caption' => 'Briefing Keselamatan Kerja di Lapangan', 
                'order' => 2,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'image_url' => 'https://example.com/images/project3-panel.jpg', 
                'caption' => 'Testing Panel LVMDP Klien Industri', 
                'order' => 3,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'image_url' => 'https://example.com/images/project4-solar.jpg', 
                'caption' => 'Instalasi Sistem Tenaga Surya Atap', 
                'order' => 4,
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}
