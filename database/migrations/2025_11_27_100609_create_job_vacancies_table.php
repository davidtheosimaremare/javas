<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('job_vacancies', function (Blueprint $table) {
        $table->id();
        $table->string('title');      // Electrical Engineer
        $table->string('department'); // Engineering
        $table->string('type');       // Full Time / Contract
        $table->string('location');   // Cikarang (Site)
        $table->string('experience'); // Min. 3 Tahun
        $table->text('description');  // Deskripsi singkat
        $table->boolean('is_active')->default(true); // Status lowongan
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
