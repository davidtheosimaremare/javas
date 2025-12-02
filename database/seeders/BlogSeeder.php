<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('blogs')->truncate();

        // Data Dummy (Saya ambil sebagian dari contoh Anda)
        $blogs = [
            ['JBB Rampungkan Instalasi Gardu Induk 150kV', 'Berita', 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e'],
            ['Program JBB Peduli: Elektrifikasi Gratis', 'CSR', 'https://images.unsplash.com/photo-1593113598332-cd288d649433'],
            ['Pencapaian 1 Juta Jam Kerja Zero Accident', 'Safety', 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1'],
            ['Implementasi Sistem SCADA Terbaru', 'Teknologi', 'https://images.unsplash.com/photo-1581092160562-40aa08e78837'],
            ['Kunjungan Kerja Kementerian ESDM', 'Berita', 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09'],
            ['JBB Raih Penghargaan Best EPC 2025', 'Award', 'https://images.unsplash.com/photo-1531403009284-440f080d1e12'],
            ['Training Sertifikasi K3 Listrik', 'Internal', 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d'],
            ['Kontrak Kerjasama Maintenance Semen Indonesia', 'Proyek', 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7'],
            ['JBB Dukung Transisi Energi Hijau', 'Berita', 'https://images.unsplash.com/photo-1593941707882-a5bba14938c7'],
            ['Pameran Electric & Power Indonesia 2025', 'Event', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87'],
            // Tambahkan data dummy tambahan untuk tes pagination
            ['Inovasi Kabel Bawah Tanah Terbaru', 'Teknologi', 'https://images.unsplash.com/photo-1498084393753-b411b2d26b34'],
            ['Gathering Tahunan Karyawan JBB', 'Internal', 'https://images.unsplash.com/photo-1511632765486-a01980e01a18'],
        ];

        foreach ($blogs as $index => $item) {
            DB::table('blogs')->insert([
                'title' => $item[0],
                'slug' => Str::slug($item[0]),
                'category' => $item[1],
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'content' => '<p>Isi berita lengkap disini...</p>',
                'image' => $item[2], // Menggunakan link external dulu
                'published_at' => now()->subDays($index), // Tanggal mundur
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}