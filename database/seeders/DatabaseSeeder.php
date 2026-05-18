<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin JBB',
            'email' => 'admin@jbb.co.id',
            'password' => bcrypt('password123'),
        ]);

        $this->call([
            CompanyProfileSeeder::class,
            FooterLinkSeeder::class,
            SliderSeeder::class,
            ServiceSeeder::class,
            StatisticSeeder::class,
            BlogSeeder::class,
            PageSettingSeeder::class,
            ProjectSeeder::class,
            AboutContentSeeder::class,
            VmItemSeeder::class,
            GalleryItemSeeder::class,
            TeamSeeder::class,
            CareerSeeder::class,
            NewsSeeder::class
        ]);

    }
}
