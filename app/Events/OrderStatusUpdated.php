<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $orderId,
        public readonly string $status,
        public readonly bool $greeted,
        public readonly string $tenantSlug,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('kitchen.' . $this->tenantSlug),
            new Channel('order.' . $this->orderId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'status'   => $this->status,
            'greeted'  => $this->greeted,
        ];
    }
}
