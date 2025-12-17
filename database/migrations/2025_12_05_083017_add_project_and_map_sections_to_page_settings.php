<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada agar tidak error jika dijalankan ulang
            if (!Schema::hasColumn('page_settings', 'project_title')) {
                $table->string('project_title')->nullable()->default('Daftar Proyek Terkini')->after('hero_bg_path');
            }
            
            if (!Schema::hasColumn('page_settings', 'project_description')) {
                $table->text('project_description')->nullable()->after('project_title');
            }

            if (!Schema::hasColumn('page_settings', 'map_title')) {
                $table->string('map_title')->nullable()->default('Peta Sebaran Proyek')->after('project_description');
            }

            if (!Schema::hasColumn('page_settings', 'map_description')) {
                $table->text('map_description')->nullable()->after('map_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            $table->dropColumn([
                'project_title', 
                'project_description', 
                'map_title', 
                'map_description'
            ]);
        });
    }
};