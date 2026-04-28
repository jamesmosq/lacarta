<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $table = 'tables';

    protected $fillable = ['name', 'qr_code', 'active', 'occupied', 'assigned_waiter_id', 'assigned_at', 'greeted_at'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    public function assignedWaiter()
    {
        return $this->belongsTo(TenantUser::class, 'assigned_waiter_id');
    }
}
