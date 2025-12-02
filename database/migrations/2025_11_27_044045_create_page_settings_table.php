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
    Schema::create('page_settings', function (Blueprint $table) {
        $table->id();
        $table->string('page_slug')->unique(); // Slug halaman: 'about', 'career', 'news.index'
        $table->string('hero_title'); // Judul yang muncul di Hero Section
        $table->string('hero_bg_path')->nullable(); // Path gambar background
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_settings');
    }
};
