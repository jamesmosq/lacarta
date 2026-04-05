<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;

class TenantUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password', 'role', 'available'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isOwner(): bool   { return $this->role === 'owner'; }
    public function isWaiter(): bool  { return $this->role === 'waiter'; }
    public function isKitchen(): bool { return $this->role === 'kitchen'; }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class, 'user_id');
    }

    public function activeShift()
    {
        return $this->shifts()->whereNull('ended_at')->latest('started_at')->first();
    }

    public static function roleLabel(string $role): string
    {
        return match($role) {
            'owner'   => 'Dueño',
            'waiter'  => 'Mesero',
            'kitchen' => 'Cocina',
            default   => $role,
        };
    }
}
