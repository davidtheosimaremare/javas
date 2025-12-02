<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CoreValue;
use App\Models\JobVacancy;

class CareerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // 1. Core Values
    $values = [
        ['title' => 'Safety First', 'description' => 'Kami menempatkan keselamatan di atas segalanya. Zero accident adalah target mutlak.', 'image' => 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?q=80&w=600&auto=format&fit=crop'],
        ['title' => 'Integrity', 'description' => 'Kejujuran dan transparansi adalah mata uang kami dalam membangun kepercayaan.', 'image' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=600&auto=format&fit=crop'],
        ['title' => 'Innovation', 'description' => 'Selalu beradaptasi dengan teknologi kelistrikan terbaru untuk efisiensi energi.', 'image' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600&auto=format&fit=crop'],
        ['title' => 'Teamwork', 'description' => 'Kekuatan kami terletak pada kolaborasi solid antar divisi.', 'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=600&auto=format&fit=crop'],
    ];
    foreach($values as $val) { CoreValue::create($val); }

    // 2. Job Vacancies
    $jobs = [
        ['title' => 'Electrical Engineer', 'department' => 'Engineering', 'type' => 'Full Time', 'location' => 'Cikarang (Site)', 'experience' => 'Min. 3 Tahun', 'description' => 'Bertanggung jawab atas desain sistem kelistrikan dan perhitungan beban.'],
        ['title' => 'HSE Officer (K3)', 'department' => 'Safety', 'type' => 'Contract', 'location' => 'Kalimantan Timur', 'experience' => 'Min. 2 Tahun', 'description' => 'Memastikan operasional proyek berjalan sesuai standar K3.'],
        ['title' => 'Project Administrator', 'department' => 'Project Support', 'type' => 'Full Time', 'location' => 'Jakarta HQ', 'experience' => 'Fresh Graduate', 'description' => 'Mengelola dokumen proyek dan laporan harian.'],
        ['title' => 'Site Manager', 'department' => 'Construction', 'type' => 'Full Time', 'location' => 'Sumatera Selatan', 'experience' => 'Min. 5 Tahun', 'description' => 'Memimpin pelaksanaan proyek di lapangan.'],
    ];
    foreach($jobs as $job) { JobVacancy::create($job); }
}
}
