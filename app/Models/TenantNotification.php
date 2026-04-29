<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantNotification extends Model
{
    protected $connection = 'pgsql_central';
    protected $table = 'tenant_notifications';
    protected $fillable = ['tenant_id', 'message', 'sent_by', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
