<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\Order;
use Tests\TenantTestCase;

class TenantInventoryTest extends TenantTestCase
{
    public function test_inventory_page_requires_authentication(): void
    {
        tenancy()->initialize($this->tenant);

        $response = $this->get("/{$this->tenant->id}/admin/inventario");
        $response->assertRedirect("/{$this->tenant->id}/admin/login");

        tenancy()->end();
    }

    public function test_inventory_page_returns_ok(): void
    {
        $this->actingAsTenantUser();

        $response = $this->get("/{$this->tenant->id}/admin/inventario");
        $response->assertStatus(200);

        tenancy()->end();
    }

    public function test_can_create_ingredient(): void
    {
        $this->actingAsTenantUser();

        $response = $this->post("/{$this->tenant->id}/admin/inventario/ingrediente", [
            'name'      => 'Carne de res',
            'unit'      => 'kg',
            'stock'     => 10,
            'min_stock' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ingredients', ['name' => 'Carne de res', 'unit' => 'kg']);

        tenancy()->end();
    }

    public function test_create_ingredient_validates_required_fields(): void
    {
        $this->actingAsTenantUser();

        $response = $this->post("/{$this->tenant->id}/admin/inventario/ingrediente", []);
        $response->assertSessionHasErrors(['name', 'unit', 'stock', 'min_stock']);

        tenancy()->end();
    }

    public function test_can_restock_ingredient(): void
    {
        $this->actingAsTenantUser();

        $ingredient = Ingredient::create(['name' => 'Arroz', 'unit' => 'kg', 'stock' => 5, 'min_stock' => 2]);

        $response = $this->patch(
            "/{$this->tenant->id}/admin/inventario/ingrediente/{$ingredient->id}/restock",
            ['cantidad' => 3]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id, 'stock' => 8]);

        tenancy()->end();
    }

    public function test_can_delete_ingredient(): void
    {
        $this->actingAsTenantUser();

        $ingredient = Ingredient::create(['name' => 'Temporal', 'unit' => 'unidad', 'stock' => 1, 'min_stock' => 0]);

        $this->delete("/{$this->tenant->id}/admin/inventario/ingrediente/{$ingredient->id}");
        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);

        tenancy()->end();
    }

    public function test_can_link_ingredient_to_dish(): void
    {
        $this->actingAsTenantUser();

        $cat        = Category::create(['name' => 'Entradas', 'order' => 1]);
        $dish       = Dish::create(['category_id' => $cat->id, 'name' => 'Empanadas', 'price' => 5000, 'available' => true]);
        $ingredient = Ingredient::create(['name' => 'Harina', 'unit' => 'kg', 'stock' => 10, 'min_stock' => 1]);

        $response = $this->post(
            "/{$this->tenant->id}/admin/inventario/plato/{$dish->id}/ingrediente",
            ['ingredient_id' => $ingredient->id, 'quantity' => 0.2]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('dish_ingredients', [
            'dish_id'       => $dish->id,
            'ingredient_id' => $ingredient->id,
        ]);

        tenancy()->end();
    }

    public function test_can_unlink_ingredient_from_dish(): void
    {
        $this->actingAsTenantUser();

        $cat        = Category::create(['name' => 'Entradas', 'order' => 1]);
        $dish       = Dish::create(['category_id' => $cat->id, 'name' => 'Empanadas', 'price' => 5000, 'available' => true]);
        $ingredient = Ingredient::create(['name' => 'Harina', 'unit' => 'kg', 'stock' => 10, 'min_stock' => 1]);
        $dish->ingredients()->attach($ingredient->id, ['quantity' => 0.2]);

        $this->delete("/{$this->tenant->id}/admin/inventario/plato/{$dish->id}/ingrediente/{$ingredient->id}");

        $this->assertDatabaseMissing('dish_ingredients', [
            'dish_id'       => $dish->id,
            'ingredient_id' => $ingredient->id,
        ]);

        tenancy()->end();
    }

    public function test_placing_order_deducts_ingredient_stock(): void
    {
        tenancy()->initialize($this->tenant);

        $cat        = Category::create(['name' => 'Entradas', 'order' => 1]);
        $dish       = Dish::create(['category_id' => $cat->id, 'name' => 'Empanadas', 'price' => 5000, 'available' => true]);
        $ingredient = Ingredient::create(['name' => 'Harina', 'unit' => 'kg', 'stock' => 10, 'min_stock' => 1]);
        $dish->ingredients()->attach($ingredient->id, ['quantity' => 0.2]); // 0.2 kg por empanada

        tenancy()->end();

        // 2 empanadas → debe descontar 0.4 kg
        $this->post("/{$this->tenant->id}/menu/pedido", [
            'customer_name' => 'Cliente Test',
            'items'         => [['dish_id' => $dish->id, 'quantity' => 2]],
        ]);

        tenancy()->initialize($this->tenant);
        $this->assertDatabaseHas('ingredients', [
            'id'    => $ingredient->id,
            'stock' => 9.6,
        ]);
        tenancy()->end();
    }

    public function test_dish_disables_when_ingredient_runs_out(): void
    {
        tenancy()->initialize($this->tenant);

        $cat        = Category::create(['name' => 'Entradas', 'order' => 1]);
        $dish       = Dish::create(['category_id' => $cat->id, 'name' => 'Empanadas', 'price' => 5000, 'available' => true]);
        $ingredient = Ingredient::create(['name' => 'Harina', 'unit' => 'kg', 'stock' => 0.3, 'min_stock' => 1]);
        // stock (0.3) ya esta bajo el minimo (1), pero la disponibilidad se recalcula al llegar un pedido
        $dish->ingredients()->attach($ingredient->id, ['quantity' => 0.2]);

        tenancy()->end();

        // Pedir 2 unidades → consume 0.4 kg → stock queda en 0 → plato se deshabilita
        $this->post("/{$this->tenant->id}/menu/pedido", [
            'customer_name' => 'Cliente Test',
            'items'         => [['dish_id' => $dish->id, 'quantity' => 2]],
        ]);

        tenancy()->initialize($this->tenant);
        $this->assertDatabaseHas('dishes', ['id' => $dish->id, 'available' => false]);
        tenancy()->end();
    }

    public function test_restock_reenables_dish(): void
    {
        $this->actingAsTenantUser();

        $cat        = Category::create(['name' => 'Entradas', 'order' => 1]);
        $dish       = Dish::create(['category_id' => $cat->id, 'name' => 'Empanadas', 'price' => 5000, 'available' => false]);
        $ingredient = Ingredient::create(['name' => 'Harina', 'unit' => 'kg', 'stock' => 0, 'min_stock' => 1]);
        $dish->ingredients()->attach($ingredient->id, ['quantity' => 0.2]);

        // Reponer stock por encima del mínimo
        $this->patch(
            "/{$this->tenant->id}/admin/inventario/ingrediente/{$ingredient->id}/restock",
            ['cantidad' => 5]
        );

        $this->assertDatabaseHas('dishes', ['id' => $dish->id, 'available' => true]);

        tenancy()->end();
    }
}
