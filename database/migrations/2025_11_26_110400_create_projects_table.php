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
    Schema::create('projects', function (Blueprint $table) {
        $table->id();
        
        // --- DATA UTAMA ---
        $table->string('title');
        $table->string('slug')->unique(); // Wajib ada untuk URL cantik
        $table->string('hero_image'); // Kita pakai nama baru langsung
        $table->text('overview');     // Kita pakai nama baru langsung
        
        // --- FILTERING & STATUS (Dari file lama Anda) ---
        $table->integer('order')->default(0);
        $table->boolean('is_active')->default(true);

        // --- DATA DETAIL (Tambahan Baru) ---
        $table->string('client')->nullable();
        $table->string('location')->nullable();
        $table->string('category')->nullable(); // Power Generation, etc
        $table->string('year')->nullable();
        $table->string('status')->default('Completed'); // Completed/On Progress
        $table->string('scope')->nullable(); // EPC, etc
        
        // --- DESKRIPSI PANJANG ---
        $table->text('challenge')->nullable();
        $table->text('solution')->nullable();

        // --- DATA PETA (MAP) ---
        $table->string('map_area')->nullable(); // Nama area di peta
        $table->string('coord_top')->nullable(); // CSS top %
        $table->string('coord_left')->nullable(); // CSS left %
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
