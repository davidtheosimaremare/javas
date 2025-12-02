<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan tabel sebelum seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('projects')->truncate();
        DB::table('project_galleries')->truncate();
        DB::table('project_testimonials')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $projects = [
            [
                // PROYEK 1: GENSET
                'title' => 'Instalasi Genset & Panel Sinkronisasi 2x500kVA',
                'overview' => 'Proyek peningkatan keandalan pasokan listrik cadangan (backup power) di area operasional tambang utama dengan dua unit Genset Silent Type.',
                
                // --- IMAGE LOKAL ---
                'hero_image' => '/storage/projects/project-1.jpg', 
                
                'order' => 1,
                'is_active' => true,
                'client' => 'PT Bukit Asam',
                'location' => 'Sumatera Selatan',
                'category' => 'Power Generation',
                'year' => '2024',
                'status' => 'Completed',
                'scope' => 'EPC',
                'challenge' => 'Lokasi remote dengan akses terbatas dan kebutuhan switching seamless untuk data center.',
                'solution' => 'Implementasi Panel ATS/AMF dengan modul sinkronisasi DeepSea Electronics.',
                'map_area' => 'Sumatera',
                'coord_top' => '60%', 
                'coord_left' => '25%',
            ],
            [
                // PROYEK 2: CUBICLE / PANEL
                'title' => 'Supply & Installation Cubicle MVMDP 20kV',
                'overview' => 'Penyediaan panel tegangan menengah untuk pabrik manufaktur baru, menjamin distribusi daya yang stabil.',
                
                // --- IMAGE LOKAL ---
                'hero_image' => '/storage/projects/project-2.jpg',
                
                'order' => 2,
                'is_active' => true,
                'client' => 'PT Hokiindo Raya',
                'location' => 'Tangerang Selatan',
                'category' => 'Industrial Installation',
                'year' => '2024',
                'status' => 'On Progress',
                'scope' => 'Procurement',
                'challenge' => 'Jadwal shutdown pabrik singkat (kurang dari 24 jam).',
                'solution' => 'Pra-rakit komponen di workshop dan kerja paralel 3 shift.',
                'map_area' => 'Banten',
                'coord_top' => '70%', 
                'coord_left' => '32%',
            ],
            [
                // PROYEK 3: TRAFO / GARDU
                'title' => 'Pemasangan Trafo Daya 60MVA & Gardu Induk',
                'overview' => 'Pembangunan gardu induk baru untuk mendukung operasional smelter nikel.',
                
                // --- IMAGE LOKAL ---
                'hero_image' => '/storage/projects/project-3.jpg',
                
                'order' => 3,
                'is_active' => true,
                'client' => 'PT Nickel Smelter Indonesia',
                'location' => 'Sulawesi Selatan',
                'category' => 'High Voltage',
                'year' => '2023',
                'status' => 'Completed',
                'scope' => 'EPC',
                'challenge' => 'Medan tanah tidak stabil untuk beban trafo berat.',
                'solution' => 'Penggunaan pondasi bore pile khusus dan soil test mendalam.',
                'map_area' => 'Sulawesi',
                'coord_top' => '55%', 
                'coord_left' => '55%',
            ],
            [
                // PROYEK 4: SOLAR PANEL / LAINNYA
                'title' => 'Instalasi Solar Panel Rooftop 1MWp',
                'overview' => 'Pemanfaatan atap pabrik untuk energi terbarukan guna efisiensi biaya listrik jangka panjang.',
                
                // --- IMAGE LOKAL ---
                'hero_image' => '/storage/projects/project-4.jpg',
                
                'order' => 4,
                'is_active' => true,
                'client' => 'PT Green Factory',
                'location' => 'Cikarang, Jawa Barat',
                'category' => 'Renewable Energy',
                'year' => '2023',
                'status' => 'Completed',
                'scope' => 'EPC',
                'challenge' => 'Struktur atap lama perlu perkuatan sebelum dipasang panel.',
                'solution' => 'Analisis struktur ulang dan penambahan rangka penyangga ringan namun kuat.',
                'map_area' => 'Jawa Barat',
                'coord_top' => '68%', 
                'coord_left' => '34%',
            ]
        ];

        foreach ($projects as $data) {
            // 1. Insert Project
            $projectId = DB::table('projects')->insertGetId(array_merge($data, [
                'slug' => Str::slug($data['title']),
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // 2. Dummy Gallery (Saya set gambar galeri pertama = gambar hero agar nyambung)
            // Gambar sisanya tetap dummy online agar slide tidak kosong, 
            // kecuali Anda punya file project-1-detail.jpg dsb.
            $galleryImages = [
                $data['hero_image'], // Gunakan hero image sebagai foto pertama di galeri
                'https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1498084393753-b411b2d26b34?q=80&w=600&auto=format&fit=crop',
            ];

            foreach ($galleryImages as $img) {
                DB::table('project_galleries')->insert([
                    'project_id' => $projectId,
                    'image_path' => $img,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3. Dummy Testimoni
            DB::table('project_testimonials')->insert([
                'project_id' => $projectId,
                'name' => 'Bpk. Klien ' . $projectId, // Nama random
                'position' => 'Project Manager',
                'quote' => 'Pelayanan JBB sangat memuaskan, teknisi handal dan tepat waktu.',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=200&auto=format&fit=crop',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}