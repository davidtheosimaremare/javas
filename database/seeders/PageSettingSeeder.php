<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('page_settings')->truncate();

        $pages = [
            // [page_slug (Nama Route), hero_title, hero_bg_path (di storage/public/images/hero/)]
            ['about', 'Profil Perusahaan', '/images/hero/about-bg.jpg'],
            ['career', 'Karir & Kesempatan', '/images/hero/career-bg.jpg'],
            ['contact', 'Hubungi Kami', '/images/hero/contact-bg.jpg'],
            ['news.index', 'Berita & Artikel', '/images/hero/news-bg.jpg'],
            ['project.index', 'Portofolio Proyek', '/images/hero/project-bg.jpg'],
            // Jika Hero Page digunakan di Home, tambahkan:
            // ['home', 'Solusi EPC Power Terintegrasi', '/images/hero/home-bg.jpg'], 
        ];

        foreach ($pages as $page) {
            DB::table('page_settings')->insert([
                'page_slug' => $page[0],
                'hero_title' => $page[1],
                'hero_bg_path' => $page[2],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}