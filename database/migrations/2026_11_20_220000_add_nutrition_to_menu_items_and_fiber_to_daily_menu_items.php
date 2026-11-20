<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->decimal('calories', 8, 2)->nullable()->after('photo');
            $table->decimal('protein', 8, 2)->nullable()->after('calories');
            $table->decimal('carbs', 8, 2)->nullable()->after('protein');
            $table->decimal('fiber', 8, 2)->nullable()->after('carbs');
        });

        Schema::table('daily_menu_items', function (Blueprint $table) {
            $table->decimal('fiber', 8, 2)->nullable()->after('fat');
        });
    }

    public function down(): void
    {
        Schema::table('daily_menu_items', function (Blueprint $table) {
            $table->dropColumn('fiber');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['calories', 'protein', 'carbs', 'fiber']);
        });
    }
};
