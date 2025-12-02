<?php

namespace Database\Seeders;

// File: database/seeders/TeamSeeder.php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('teams')->truncate();
        
        // Memasukkan lebih dari 1 data anggota tim
        DB::table('teams')->insert([
            [
                'name' => 'Dr. Ir. Budi Santoso, MBA', 
                'title' => 'Direktur Utama', 
                'image_url' => 'https://example.com/images/team-budi.jpg', 
                'order' => 1,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Rina Wijaya, S.T., M.Eng.', 
                'title' => 'Direktur Operasional', 
                'image_url' => 'https://example.com/images/team-rina.jpg', 
                'order' => 2,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Faisal Ahmad, S.E., Ak.', 
                'title' => 'Chief Financial Officer', 
                'image_url' => 'https://example.com/images/team-faisal.jpg', 
                'order' => 3,
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}
