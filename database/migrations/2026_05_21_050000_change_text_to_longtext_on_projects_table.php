<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->longText('overview')->change();
            $table->longText('challenge')->change()->nullable();
            $table->longText('solution')->change()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('overview')->change();
            $table->text('challenge')->change()->nullable();
            $table->text('solution')->change()->nullable();
        });
    }
};
