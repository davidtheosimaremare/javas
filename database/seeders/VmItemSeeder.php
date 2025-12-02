<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VmItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        DB::table('vm_items')->truncate();
        
        DB::table('vm_items')->insert([
            // VISI (Hanya 1 item dengan type 'visi')
            [
                'type' => 'visi', 
                'content' => 'Menjadi pemimpin pasar dalam solusi energi terintegrasi di Asia Tenggara dengan fokus pada inovasi dan keberlanjutan.', 
                'order' => 1,
                'created_at' => now(), 'updated_at' => now()
            ],
            
            // MISI (Lebih dari 1 item dengan type 'misi')
            [
                'type' => 'misi', 
                'content' => 'Memberikan layanan EPC kelas dunia yang memenuhi standar kualitas internasional dan tepat waktu.', 
                'order' => 2,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'type' => 'misi', 
                'content' => 'Mengembangkan sumber daya manusia yang kompeten, beretika tinggi, dan berorientasi pada keselamatan kerja.', 
                'order' => 3,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'type' => 'misi', 
                'content' => 'Membangun kemitraan strategis yang saling menguntungkan dengan klien, pemasok, dan masyarakat.', 
                'order' => 4,
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}
