<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use Tests\TenantTestCase;

class TenantReportTest extends TenantTestCase
{
    public function test_reports_page_requires_authentication(): void
    {
        tenancy()->initialize($this->tenant);

        $response = $this->get("/{$this->tenant->id}/admin/reportes");
        $response->assertRedirect("/{$this->tenant->id}/admin/login");

        tenancy()->end();
    }

    public function test_reports_page_returns_ok(): void
    {
        $this->actingAsTenantUser();

        $response = $this->get("/{$this->tenant->id}/admin/reportes");
        $response->assertStatus(200);

        tenancy()->end();
    }

    public function test_reports_shows_total_revenue(): void
    {
        $this->actingAsTenantUser();

        Order::create(['status' => Order::STATUS_DELIVERED, 'total' => 25000]);
        Order::create(['status' => Order::STATUS_DELIVERED, 'total' => 15000]);

        $response = $this->get("/{$this->tenant->id}/admin/reportes?periodo=mes");
        $response->assertStatus(200);
        $response->assertSee('40.000');

        tenancy()->end();
    }

    public function test_reports_shows_order_count(): void
    {
        $this->actingAsTenantUser();

        Order::create(['status' => Order::STATUS_PENDING,   'total' => 10000]);
        Order::create(['status' => Order::STATUS_DELIVERED, 'total' => 20000]);

        $response = $this->get("/{$this->tenant->id}/admin/reportes?periodo=mes");
        $response->assertStatus(200);
        $response->assertSee('2');

        tenancy()->end();
    }

    public function test_reports_shows_top_dishes(): void
    {
        $this->actingAsTenantUser();

        $cat  = Category::create(['name' => 'Entradas', 'order' => 1]);
        $dish = Dish::create(['category_id' => $cat->id, 'name' => 'Empanadas', 'price' => 5000, 'available' => true]);

        $order = Order::create(['status' => Order::STATUS_DELIVERED, 'total' => 15000]);
        OrderItem::create(['order_id' => $order->id, 'dish_id' => $dish->id, 'quantity' => 3, 'unit_price' => 5000]);

        $response = $this->get("/{$this->tenant->id}/admin/reportes?periodo=mes");
        $response->assertSee('Empanadas');

        tenancy()->end();
    }

    public function test_reports_export_returns_csv(): void
    {
        $this->actingAsTenantUser();

        Order::create(['status' => Order::STATUS_DELIVERED, 'total' => 10000, 'customer_name' => 'Cliente CSV']);

        $response = $this->get("/{$this->tenant->id}/admin/reportes/exportar?periodo=mes");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        tenancy()->end();
    }

    public function test_reports_period_filter_hoy(): void
    {
        $this->actingAsTenantUser();

        $response = $this->get("/{$this->tenant->id}/admin/reportes?periodo=hoy");
        $response->assertStatus(200);

        tenancy()->end();
    }

    public function test_reports_period_filter_custom(): void
    {
        $this->actingAsTenantUser();

        $response = $this->get("/{$this->tenant->id}/admin/reportes?periodo=custom&desde=2026-01-01&hasta=2026-12-31");
        $response->assertStatus(200);

        tenancy()->end();
    }
}
