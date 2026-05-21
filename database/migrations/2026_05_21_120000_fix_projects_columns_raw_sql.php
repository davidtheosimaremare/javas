<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix all project columns via raw SQL (no doctrine/dbal required)
        DB::statement('ALTER TABLE projects MODIFY hero_image VARCHAR(255) NULL');
        DB::statement('ALTER TABLE projects MODIFY overview LONGTEXT NULL');
        DB::statement('ALTER TABLE projects MODIFY challenge LONGTEXT NULL');
        DB::statement('ALTER TABLE projects MODIFY solution LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE projects MODIFY hero_image VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE projects MODIFY overview TEXT NOT NULL');
        DB::statement('ALTER TABLE projects MODIFY challenge TEXT NULL');
        DB::statement('ALTER TABLE projects MODIFY solution TEXT NULL');
    }
};
