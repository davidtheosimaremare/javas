<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            $table->string('career_intro_title')->nullable()->after('map_description');
            $table->text('career_intro_desc')->nullable()->after('career_intro_title');

            // Seksi Values
            $table->string('career_values_title')->nullable()->after('career_intro_desc');
            $table->string('career_values_subtitle')->nullable()->after('career_values_title');

            // Seksi Jobs
            $table->string('career_jobs_title')->nullable()->after('career_values_subtitle');
            $table->string('career_jobs_subtitle')->nullable()->after('career_jobs_title');
        });
    }

    public function down(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            $table->dropColumn([
                'career_intro_title', 'career_intro_desc',
                'career_values_title', 'career_values_subtitle',
                'career_jobs_title', 'career_jobs_subtitle'
            ]);
        });
    }
};