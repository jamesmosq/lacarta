<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TableAssignedToWaiter implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $tableId,
        public readonly string $tableName,
        public readonly int $waiterId,
        public readonly string $tenantSlug,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('waiter.' . $this->tenantSlug . '.' . $this->waiterId)];
    }

    public function broadcastAs(): string
    {
        return 'table.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'table_id'   => $this->tableId,
            'table_name' => $this->tableName,
        ];
    }
}
