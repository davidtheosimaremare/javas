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
        Schema::create('vm_items', function (Blueprint $table) {
            $table->id();
            // 'visi' (untuk satu item) atau 'misi' (untuk banyak item)
            $table->enum('type', ['visi', 'misi']); 
            $table->text('content'); // Isi dari Visi atau Misi
            $table->integer('order')->default(0); // Urutan tampil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vm_items');
    }
};
