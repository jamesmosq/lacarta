<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['user_id', 'started_at', 'ended_at', 'expected_hours', 'notes'];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    /** Minutos trabajados reales (null si el turno no ha terminado). */
    public function workedMinutes(): ?int
    {
        if (!$this->ended_at) return null;
        return (int) $this->started_at->diffInMinutes($this->ended_at);
    }

    /** Minutos de horas extra (negativo = se fue antes). */
    public function overtimeMinutes(): ?int
    {
        $worked = $this->workedMinutes();
        if ($worked === null) return null;
        return $worked - ($this->expected_hours * 60);
    }

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }
}
