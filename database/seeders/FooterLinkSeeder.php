<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FooterLinkSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('footer_links')->truncate();

        $data = [
            // INTERNAL LINKS (Tautan)
            ['internal_link', 'Layanan & Produk', '/product', 1],
            ['internal_link', 'Portofolio Proyek', '/project', 2],
            ['internal_link', 'Karir', '/career', 3],
            ['internal_link', 'Berita & Artikel', '/news', 4],
            ['internal_link', 'K3 / Safety', '/safety', 5],
            
            // PARTNER LINKS
            ['partner', 'Siemens Indonesia', 'https://www.siemens.co.id', 1],
            ['partner', 'Schneider Electric', 'https://www.se.com', 2],
            ['partner', 'PLN (Persero)', 'https://www.pln.co.id', 3],
            ['partner', 'Kementerian ESDM', 'https://esdm.go.id', 4],
            ['partner', 'JBB Group', 'https://jbbgroup.com', 5],
        ];

        foreach ($data as $item) {
            DB::table('footer_links')->insert([
                'type' => $item[0],
                'name' => $item[1],
                'url' => $item[2],
                'order' => $item[3],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}