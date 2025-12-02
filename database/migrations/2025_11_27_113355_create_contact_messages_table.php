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
    Schema::create('contact_messages', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('company');
        $table->string('phone');
        $table->string('email');
        $table->string('service_type'); // Instalasi, Maintenance, dll
        $table->string('location');
        $table->text('message');
        $table->string('file_path')->nullable(); // Untuk lampiran file
        $table->string('status')->default('new'); // new, read, contacted
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
