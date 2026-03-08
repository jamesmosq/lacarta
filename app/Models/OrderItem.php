<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'dish_id', 'quantity', 'unit_price', 'notes'];

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }

    public function subtotal(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
