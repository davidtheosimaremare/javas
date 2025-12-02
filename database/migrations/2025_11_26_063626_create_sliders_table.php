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
    Schema::create('sliders', function (Blueprint $table) {
        $table->id();
        $table->string('title');        // Main Title (Judul Besar)
        $table->text('description');    // Slide Desc (Deskripsi)
        $table->string('nav_title');    // Nav Title (IDENTITAS, VISI, dll)
        $table->string('image');        // Path gambar
        $table->integer('order')->default(0); // Urutan slider
        $table->boolean('is_active')->default(true); // Status aktif/tidak
        $table->string('link')->default('#');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
