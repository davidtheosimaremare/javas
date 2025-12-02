<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel dulu agar tidak duplikat saat dijalankan ulang
        DB::table('sliders')->truncate();

        $slides = [
            [
                'title' => 'Kekuatan EPC Power Terintegrasi',
                'nav_title' => 'IDENTITAS',
                'description' => 'Kami menyatukan rekayasa, pengadaan, dan konstruksi. JBB mengelola proyek kelistrikan paling kompleks secara terintegrasi—dari konsep hingga menyala.',
                // Path mengarah ke folder storage/app/public/sliders/
                'image' => '/storage/sliders/banner-slider-1.jpg', 
                'order' => 1,
                'is_active' => true,
                'link' => '/about'
            ],
            [
                'title' => 'Presisi Tanpa Kompromi',
                'nav_title' => 'STANDAR',
                'description' => 'Kualitas adalah standar kami. Kami menerapkan Presisi mutlak dalam desain dan instalasi untuk menjamin sistem yang andal, aman, dan berfungsi sempurna.',
                'image' => '/storage/sliders/banner-slider-2.jpg',
                'order' => 2,
                'is_active' => true,
                'link' => '/project'
            ],
            [
                'title' => 'Akselerasi Pasti, Eksekusi Terjamin',
                'nav_title' => 'KOMITMEN',
                'description' => 'Kami bekerja dengan Kecepatan dan metodologi teruji. JBB adalah Garansi Anda untuk proyek yang selesai tepat waktu, sesuai anggaran, dan melampaui ekspektasi.',
                'image' => '/storage/sliders/banner-slider-3.jpg',
                'order' => 3,
                'is_active' => true,
                'link' => '/services'
            ],
            [
                'title' => 'Mitra Andal untuk Energi Berkelanjutan',
                'nav_title' => 'VISI',
                'description' => 'Kami membangun untuk jangka panjang. JBB mengintegrasikan solusi Berkelanjutan yang efisien, memastikan aset kelistrikan Anda siap untuk tantangan energi masa depan.',
                'image' => '/storage/sliders/banner-slider-4.jpg',
                'order' => 4,
                'is_active' => true,
                'link' => '/contact'
            ],
        ];

        foreach ($slides as $slide) {
            DB::table('sliders')->insert(array_merge($slide, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}