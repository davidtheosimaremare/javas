<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Raw SQL - tidak butuh doctrine/dbal
        DB::statement('ALTER TABLE projects MODIFY hero_image VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE projects MODIFY hero_image VARCHAR(255) NOT NULL');
    }
};
