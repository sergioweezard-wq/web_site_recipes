<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->decimal('fats', 8, 1)->default(0)->after('calories');
            $table->decimal('carbs', 8, 1)->default(0)->after('fats');
            $table->decimal('proteins', 8, 1)->default(0)->after('carbs');
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['fats', 'carbs', 'proteins']);
        });
    }
};
