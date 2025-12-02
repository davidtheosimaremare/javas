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
    Schema::create('blogs', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique(); // Untuk URL cantik (news/judul-berita)
        $table->string('category');       // Berita, CSR, Award, dll
        $table->text('excerpt');          // Ringkasan pendek
        $table->longText('content');      // Isi berita lengkap (HTML)
        $table->string('image');          // Path gambar
        $table->date('published_at');     // Tanggal terbit
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
