<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id(); 
            
            // INFORMASI KONTAK & KETERANGAN
            $table->string('company_name')->default('PT JBB Javas Berkah Bistari');
            $table->string('phone_number')->nullable(); 
            $table->string('company_email')->nullable(); 
            $table->text('company_address')->nullable(); 
            $table->text('company_description')->nullable(); 

            // LOGO VARIANT BARU
            $table->string('logo_primary')->nullable();   // Logo Putih/Terang (Untuk header gelap)
            $table->string('logo_secondary')->nullable(); // Logo Warna/Gelap (Untuk header terang/putih)

            // SOSIAL MEDIA
            $table->json('social_media')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};