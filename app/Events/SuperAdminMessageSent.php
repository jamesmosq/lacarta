<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuperAdminMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $notificationId,
        public readonly string $message,
        public readonly string $tenantSlug,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tenant.' . $this->tenantSlug)];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'      => $this->notificationId,
            'message' => $this->message,
        ];
    }
}
