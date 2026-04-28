<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TenantUser;

class Order extends Model
{
    protected $fillable = ['table_id', 'user_id', 'status', 'customer_name', 'total', 'notes'];

    const STATUS_PENDING   = 'pending';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY     = 'ready';
    const STATUS_DELIVERED = 'delivered';

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function waiter()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING   => 'Pendiente',
            self::STATUS_PREPARING => 'Preparando',
            self::STATUS_READY     => 'Listo',
            self::STATUS_DELIVERED => 'Entregado',
            default                => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            self::STATUS_PENDING   => 'yellow',
            self::STATUS_PREPARING => 'blue',
            self::STATUS_READY     => 'green',
            self::STATUS_DELIVERED => 'gray',
            default                => 'gray',
        };
    }
}
