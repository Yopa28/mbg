<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_menu_id')->nullable()
                ->constrained('daily_menus')->nullOnDelete();
            $table->string('category', 50);
            $table->string('name', 100)->nullable();
            $table->string('contact', 100)->nullable();
            $table->text('message');
            $table->string('status', 30)->default('new');
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
