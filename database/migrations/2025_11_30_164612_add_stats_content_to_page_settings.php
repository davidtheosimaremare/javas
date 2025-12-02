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
    Schema::table('page_settings', function (Blueprint $table) {
        $table->string('stats_title')->nullable();
        $table->text('stats_description')->nullable();
        $table->string('stats_bg_image')->nullable(); // Untuk background section
    });
}

public function down(): void
{
    Schema::table('page_settings', function (Blueprint $table) {
        $table->dropColumn(['stats_title', 'stats_description', 'stats_bg_image']);
    });
}
};
