<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name', 'unit', 'stock', 'min_stock'];

    protected function casts(): array
    {
        return [
            'stock'     => 'decimal:3',
            'min_stock' => 'decimal:3',
        ];
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'dish_ingredients')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function stockStatus(): string
    {
        if ($this->stock <= 0) {
            return 'out';
        }
        if ($this->stock <= $this->min_stock) {
            return 'low';
        }
        return 'ok';
    }

    /**
     * Después de modificar el stock de este ingrediente,
     * recalcula la disponibilidad de todos los platos que lo usan.
     */
    public function syncDishesAvailability(): void
    {
        foreach ($this->dishes()->with('ingredients')->get() as $dish) {
            $dish->syncAvailabilityFromIngredients();
        }
    }
}
