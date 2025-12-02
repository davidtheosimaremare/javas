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
    Schema::create('core_values', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description'); // desc
        $table->string('image');     // URL atau path gambar
        $table->integer('order')->default(0); // Untuk urutan
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_values');
    }
};
