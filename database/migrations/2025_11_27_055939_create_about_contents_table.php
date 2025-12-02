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
        Schema::create('about_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page_title'); // Judul utama section (e.g., Sekilas Tentang Kami)
            $table->longText('content_html'); // Konten deskripsi panjang (bisa pakai editor HTML/TinyMCE)
            $table->string('section_subtitle')->nullable();
            $table->longText('quote_explanation_text')->nullable();
            $table->text('quote'); // Slogan atau Quote Perusahaan
            $table->string('hero_image')->nullable(); // Path gambar background hero
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_contents');
    }
};
