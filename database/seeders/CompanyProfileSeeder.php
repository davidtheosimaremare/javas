<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('company_profiles')->truncate();

        DB::table('company_profiles')->insert([
            'id' => 1,
            'company_name' => 'PT JBB Javas Berkah Bistari',
            
            'phone_number' => '(021) 123-4567',
            'company_email' => 'contact@jbb-power.com',
            'company_address' => 'Jl. Raya Serpong Kilometer 7 No.50 Blok Q, Serpong Utara, South Tangerang City, Banten 15310',
            'company_description' => 'JBB adalah perusahaan Engineering, Procurement, and Construction (EPC) Power yang berfokus pada solusi kelistrikan terintegrasi dan energi berkelanjutan.',
            
            // --- VARIANT LOGO BARU ---
            'logo_primary' => '/images/logo-light.png',     // Logo Putih
            'logo_secondary' => '/images/logo-color.png',  // Logo Warna
            
            'social_media' => json_encode([
                ['platform' => 'Instagram', 'icon' => 'bi-instagram', 'url' => '#'],
                ['platform' => 'Tiktok', 'icon' => 'bi-tiktok', 'url' => '#'],
                ['platform' => 'Twitter/X', 'icon' => 'bi-twitter-x', 'url' => '#'],
                ['platform' => 'Facebook', 'icon' => 'bi-facebook', 'url' => '#'],
                ['platform' => 'Youtube', 'icon' => 'bi-youtube', 'url' => '#'],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}