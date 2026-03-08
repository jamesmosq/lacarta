<?php

namespace Tests\Unit;

use App\Models\OrderItem;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    public function test_subtotal_multiplies_quantity_by_unit_price(): void
    {
        $item = new OrderItem(['quantity' => 3, 'unit_price' => 25000]);
        $this->assertEquals(75000, $item->subtotal());
    }

    public function test_subtotal_with_single_item(): void
    {
        $item = new OrderItem(['quantity' => 1, 'unit_price' => 38000]);
        $this->assertEquals(38000, $item->subtotal());
    }

    public function test_subtotal_with_decimal_price(): void
    {
        $item = new OrderItem(['quantity' => 2, 'unit_price' => 9999.99]);
        $this->assertEquals(19999.98, $item->subtotal());
    }

    public function test_subtotal_returns_float(): void
    {
        $item = new OrderItem(['quantity' => 2, 'unit_price' => 10000]);
        $this->assertIsFloat($item->subtotal());
    }
}
