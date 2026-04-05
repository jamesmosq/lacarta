<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_bank', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('default_price', 10, 2)->default(0);
            $table->string('category_hint')->nullable(); // nombre sugerido de categoría
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_bank');
    }
};
