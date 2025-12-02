<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            // Kita taruh setelah 'hero_bg_path' agar urutannya rapi di database
            $table->string('service_title')->nullable()->after('hero_bg_path');
            $table->text('service_description')->nullable()->after('service_title');
        });
    }

    public function down(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            $table->dropColumn(['service_title', 'service_description']);
        });
    }
};