<?php

namespace Tests\Unit;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function test_status_label_returns_correct_spanish_label(): void
    {
        $cases = [
            Order::STATUS_PENDING   => 'Pendiente',
            Order::STATUS_PREPARING => 'Preparando',
            Order::STATUS_READY     => 'Listo',
            Order::STATUS_DELIVERED => 'Entregado',
        ];

        foreach ($cases as $status => $expectedLabel) {
            $order = new Order(['status' => $status]);
            $this->assertSame($expectedLabel, $order->statusLabel());
        }
    }

    public function test_status_color_returns_correct_color(): void
    {
        $cases = [
            Order::STATUS_PENDING   => 'yellow',
            Order::STATUS_PREPARING => 'blue',
            Order::STATUS_READY     => 'green',
            Order::STATUS_DELIVERED => 'gray',
        ];

        foreach ($cases as $status => $expectedColor) {
            $order = new Order(['status' => $status]);
            $this->assertSame($expectedColor, $order->statusColor());
        }
    }

    public function test_status_constants_are_defined(): void
    {
        $this->assertSame('pending',   Order::STATUS_PENDING);
        $this->assertSame('preparing', Order::STATUS_PREPARING);
        $this->assertSame('ready',     Order::STATUS_READY);
        $this->assertSame('delivered', Order::STATUS_DELIVERED);
    }

    public function test_unknown_status_returns_fallback(): void
    {
        $order = new Order(['status' => 'unknown']);
        $this->assertSame('unknown', $order->statusLabel());
        $this->assertSame('gray', $order->statusColor());
    }
}
