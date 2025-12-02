<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AboutContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('about_contents')->truncate();
        
        DB::table('about_contents')->insert([
            'id' => 1,
            'page_title' => 'Sekilas Tentang JBB Javas Berkah Bistari',
            'section_subtitle' => 'Semangat Membangun Negeri',
            'quote_explanation_text' => 'Slogan ini bukan sekadar kata-kata, melainkan cerminan etos kerja kami. <strong>"Precision"</strong> menggambarkan ketelitian kami dalam setiap perhitungan teknis, sementara <strong>"Excellence"</strong> adalah janji kami untuk memberikan hasil akhir yang melampaui ekspektasi klien melalui inovasi berkelanjutan.',
            'content_html' => '<p>PT JBB Javas Berkah Bistari adalah perusahaan yang bergerak di bidang <strong>Engineering, Procurement, dan Construction (EPC)</strong> Power serta kontraktor instalasi listrik yang berdedikasi untuk memberikan solusi energi terbaik di Indonesia. Kami telah dipercaya menangani berbagai proyek strategis. Kami berkomitmen untuk menghadirkan kualitas pekerjaan yang presisi, aman, dan tepat waktu.</p>',
            'quote' => 'Precision in Engineering, Excellence in Execution',
            'hero_image' => 'https://example.com/images/hero-about.jpg', // Ganti dengan URL/Path dummy
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}