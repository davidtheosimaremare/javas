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
    Schema::create('project_testimonials', function (Blueprint $table) {
        $table->id();
        // Menghubungkan ke tabel projects
        $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
        $table->string('name');      // Nama pemberi testimoni
        $table->string('position');  // Jabatan
        $table->text('quote');       // Isi testimoni
        $table->string('avatar');    // Foto profil pemberi testimoni
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_testimonials');
    }
};
