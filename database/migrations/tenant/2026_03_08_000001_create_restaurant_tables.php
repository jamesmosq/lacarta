<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Usuarios del restaurante (dueño, meseros, cocineros)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('owner'); // owner, waiter, kitchen
            $table->rememberToken();
            $table->timestamps();
        });

        // Categorías del menú (Entradas, Platos fuertes, Bebidas, Postres)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Platos del menú
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->boolean('available')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Mesas del restaurante
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Mesa 1, Mesa 2, Terraza A...
            $table->string('qr_code')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Pedidos
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); // pending, preparing, ready, delivered
            $table->string('customer_name')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Ítems del pedido
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dish_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('dishes');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
    }
};
