<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use Tests\TenantTestCase;

class TenantMenuTest extends TenantTestCase
{
    public function test_menu_index_requires_authentication(): void
    {
        tenancy()->initialize($this->tenant);

        $response = $this->get("/{$this->tenant->id}/admin/menu");
        $response->assertRedirect("/{$this->tenant->id}/admin/login");

        tenancy()->end();
    }

    public function test_menu_index_returns_ok_when_authenticated(): void
    {
        $this->actingAsTenantUser();

        $response = $this->get("/{$this->tenant->id}/admin/menu");
        $response->assertStatus(200);

        tenancy()->end();
    }

    public function test_can_create_category(): void
    {
        $this->actingAsTenantUser();

        $response = $this->post("/{$this->tenant->id}/admin/menu/categoria", [
            'name'  => 'Entradas',
            'order' => 1,
        ]);

        $response->assertRedirect("/{$this->tenant->id}/admin/menu");
        $this->assertDatabaseHas('categories', ['name' => 'Entradas']);

        tenancy()->end();
    }

    public function test_create_category_validates_name(): void
    {
        $this->actingAsTenantUser();

        $response = $this->post("/{$this->tenant->id}/admin/menu/categoria", []);
        $response->assertSessionHasErrors(['name']);

        tenancy()->end();
    }

    public function test_can_create_dish(): void
    {
        $this->actingAsTenantUser();

        $category = Category::create(['name' => 'Entradas', 'order' => 1]);

        $response = $this->post("/{$this->tenant->id}/admin/menu/plato", [
            'category_id' => $category->id,
            'name'        => 'Empanadas',
            'description' => 'Deliciosas empanadas',
            'price'       => 8000,
        ]);

        $response->assertRedirect("/{$this->tenant->id}/admin/menu");
        $this->assertDatabaseHas('dishes', [
            'name'  => 'Empanadas',
            'price' => 8000,
        ]);

        tenancy()->end();
    }

    public function test_create_dish_validates_required_fields(): void
    {
        $this->actingAsTenantUser();

        $response = $this->post("/{$this->tenant->id}/admin/menu/plato", []);
        $response->assertSessionHasErrors(['category_id', 'name', 'price']);

        tenancy()->end();
    }

    public function test_can_toggle_dish_availability(): void
    {
        $this->actingAsTenantUser();

        $category = Category::create(['name' => 'Entradas', 'order' => 1]);
        $dish = Dish::create([
            'category_id' => $category->id,
            'name'        => 'Empanadas',
            'price'       => 8000,
            'available'   => true,
        ]);

        $this->patch("/{$this->tenant->id}/admin/menu/plato/{$dish->id}/disponible");
        $this->assertDatabaseHas('dishes', ['id' => $dish->id, 'available' => false]);

        $this->patch("/{$this->tenant->id}/admin/menu/plato/{$dish->id}/disponible");
        $this->assertDatabaseHas('dishes', ['id' => $dish->id, 'available' => true]);

        tenancy()->end();
    }

    public function test_can_delete_dish(): void
    {
        $this->actingAsTenantUser();

        $category = Category::create(['name' => 'Entradas', 'order' => 1]);
        $dish = Dish::create([
            'category_id' => $category->id,
            'name'        => 'Temporal',
            'price'       => 5000,
        ]);

        $this->delete("/{$this->tenant->id}/admin/menu/plato/{$dish->id}");
        $this->assertDatabaseMissing('dishes', ['id' => $dish->id]);

        tenancy()->end();
    }
}
