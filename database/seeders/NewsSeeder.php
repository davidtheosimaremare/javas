<?php

namespace Database\Seeders;
use App\Models\News;
use Illuminate\Support\Str;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
{
    $data = [
        ['category' => 'Berita', 'date' => '2025-11-18', 'title' => 'JBB Rampungkan Instalasi Gardu Induk 150kV di Kawasan Industri Batang'],
        ['category' => 'CSR', 'date' => '2025-11-17', 'title' => 'Program JBB Peduli: Elektrifikasi Gratis untuk 50 Rumah di Desa Terpencil'],
        ['category' => 'Safety', 'date' => '2025-11-15', 'title' => 'Pencapaian 1 Juta Jam Kerja Tanpa Kecelakaan (Zero Accident)'],
        ['category' => 'Teknologi', 'date' => '2025-11-14', 'title' => 'Implementasi Sistem SCADA Terbaru untuk Monitoring Real-time Panel LVMDP'],
        ['category' => 'Award', 'date' => '2025-11-05', 'title' => 'JBB Raih Penghargaan "Best EPC Contractor 2025"'],

    ];

    foreach ($data as $item) {
        News::create([
            'title' => $item['title'],
            'slug' => Str::slug($item['title']),
            'category' => $item['category'],
            'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?q=80&w=600&auto=format&fit=crop', // Dummy img
            'published_at' => $item['date'],
            'content' => 'Lorem ipsum dolor sit amet...', // Dummy content
            'summary' => 'Ringkasan berita...'
        ]);
    }
}
}
