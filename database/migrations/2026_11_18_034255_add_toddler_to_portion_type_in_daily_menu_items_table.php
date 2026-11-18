
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE daily_menu_items
            MODIFY portion_type ENUM('large', 'small', 'toddler') NOT NULL
        ");
    }

    public function down(): void
    {
        $hasToddlerData = DB::table('daily_menu_items')
            ->where('portion_type', 'toddler')
            ->exists();

        if ($hasToddlerData) {
            throw new RuntimeException(
                'Tidak dapat rollback: masih ada data porsi balita.'
            );
        }

        DB::statement("
            ALTER TABLE daily_menu_items
            MODIFY portion_type ENUM('large', 'small') NOT NULL
        ");
    }
};

