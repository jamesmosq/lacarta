<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = ['table_id', 'waiter_id', 'total', 'payment_method', 'notes', 'paid_at'];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function waiter()
    {
        return $this->belongsTo(TenantUser::class, 'waiter_id');
    }

    public function paymentMethodLabel(): string
    {
        return match($this->payment_method) {
            'efectivo'      => 'Efectivo',
            'tarjeta'       => 'Tarjeta',
            'transferencia' => 'Transferencia',
            default         => $this->payment_method,
        };
    }
}
