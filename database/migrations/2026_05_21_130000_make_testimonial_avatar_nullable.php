<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make avatar nullable - no doctrine/dbal needed
        DB::statement('ALTER TABLE project_testimonials MODIFY avatar VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE project_testimonials MODIFY avatar VARCHAR(255) NOT NULL');
    }
};
