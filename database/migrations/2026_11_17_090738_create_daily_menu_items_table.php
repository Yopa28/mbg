
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_menu_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_menu_id')
                ->constrained('daily_menus')
                ->cascadeOnDelete();

            $table->foreignId('menu_item_id')
                ->constrained('menu_items')
                ->restrictOnDelete();

            $table->enum('portion_type', ['large', 'small']);

            $table->decimal('quantity', 8, 2)->default(1);
            $table->string('unit', 50)->default('porsi');

            $table->decimal('calories', 8, 2)->nullable();
            $table->decimal('protein', 8, 2)->nullable();
            $table->decimal('carbs', 8, 2)->nullable();
            $table->decimal('fat', 8, 2)->nullable();

            $table->timestamps();

            $table->unique([
                'daily_menu_id',
                'menu_item_id',
                'portion_type',
            ], 'daily_menu_item_portion_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_menu_items');
    }
};
