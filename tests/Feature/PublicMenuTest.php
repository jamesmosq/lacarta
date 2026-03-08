<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use Tests\TenantTestCase;

class PublicMenuTest extends TenantTestCase
{
    public function test_public_menu_returns_ok(): void
    {
        tenancy()->initialize($this->tenant);

        $response = $this->get("/{$this->tenant->id}/menu");
        $response->assertStatus(200);

        tenancy()->end();
    }

    public function test_public_menu_shows_restaurant_name(): void
    {
        tenancy()->initialize($this->tenant);

        $response = $this->get("/{$this->tenant->id}/menu");
        $response->assertSee('Test Restaurant');

        tenancy()->end();
    }

    public function test_public_menu_shows_available_dishes(): void
    {
        tenancy()->initialize($this->tenant);

        $cat = Category::create(['name' => 'Entradas', 'order' => 1]);
        Dish::create(['category_id' => $cat->id, 'name' => 'Empanadas', 'price' => 8000, 'available' => true]);
        Dish::create(['category_id' => $cat->id, 'name' => 'Plato oculto', 'price' => 5000, 'available' => false]);

        tenancy()->end();

        tenancy()->initialize($this->tenant);
        $response = $this->get("/{$this->tenant->id}/menu");
        $response->assertSee('Empanadas');
        $response->assertDontSee('Plato oculto');

        tenancy()->end();
    }

    public function test_can_submit_order(): void
    {
        tenancy()->initialize($this->tenant);

        $cat  = Category::create(['name' => 'Entradas', 'order' => 1]);
        $dish = Dish::create(['category_id' => $cat->id, 'name' => 'Empanadas', 'price' => 8000, 'available' => true]);

        tenancy()->end();

        $response = $this->post("/{$this->tenant->id}/menu/pedido", [
            'customer_name' => 'Cliente Test',
            'items'         => [
                ['dish_id' => $dish->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect();

        tenancy()->initialize($this->tenant);
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Cliente Test',
            'status'        => 'pending',
            'total'         => 16000,
        ]);
        tenancy()->end();
    }

    public function test_order_requires_at_least_one_item(): void
    {
        $response = $this->post("/{$this->tenant->id}/menu/pedido", [
            'customer_name' => 'Test',
            'items'         => [],
        ]);

        $response->assertSessionHasErrors(['items']);
    }

    public function test_order_status_page_returns_ok(): void
    {
        tenancy()->initialize($this->tenant);

        $order = Order::create([
            'status' => Order::STATUS_PENDING,
            'total'  => 10000,
        ]);

        $response = $this->get("/{$this->tenant->id}/menu/pedido/{$order->id}/estado");
        $response->assertStatus(200);
        $response->assertSee("#{$order->id}");

        tenancy()->end();
    }

    public function test_order_status_shows_correct_status(): void
    {
        tenancy()->initialize($this->tenant);

        $order = Order::create([
            'status' => Order::STATUS_PREPARING,
            'total'  => 10000,
        ]);

        $response = $this->get("/{$this->tenant->id}/menu/pedido/{$order->id}/estado");
        $response->assertSee('Preparando');

        tenancy()->end();
    }
}
