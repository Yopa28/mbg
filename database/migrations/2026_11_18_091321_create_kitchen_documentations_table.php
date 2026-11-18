
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kitchen_documentations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_menu_id')
                ->constrained('daily_menus')
                ->cascadeOnDelete();

            $table->string('category', 50);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('photo_path');
            $table->decimal('temperature', 5, 2)->nullable();
            $table->dateTime('captured_at')->nullable();
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index(['daily_menu_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_documentations');
    }
};

