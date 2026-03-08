<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->default('unidad'); // unidad, kg, g, L, ml, porcion
            $table->decimal('stock', 10, 3)->default(0);
            $table->decimal('min_stock', 10, 3)->default(0); // umbral de alerta
            $table->timestamps();
        });

        // Receta: qué ingredientes usa cada plato y en qué cantidad
        Schema::create('dish_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 3); // cantidad que consume una porción del plato
            $table->timestamps();
            $table->unique(['dish_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dish_ingredients');
        Schema::dropIfExists('ingredients');
    }
};
