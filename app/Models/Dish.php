<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'price', 'image', 'available', 'order'];

    protected function casts(): array
    {
        return [
            'available' => 'boolean',
            'price'     => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'dish_ingredients')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Habilita o deshabilita el plato según el stock de sus ingredientes.
     * Si el plato no tiene ingredientes en receta, no se toca su disponibilidad.
     */
    public function syncAvailabilityFromIngredients(): void
    {
        $ingredients = $this->ingredients;

        if ($ingredients->isEmpty()) {
            return;
        }

        $hasEnoughStock = $ingredients->every(
            fn($ing) => $ing->stock > $ing->min_stock
        );

        if ($this->available !== $hasEnoughStock) {
            $this->update(['available' => $hasEnoughStock]);
        }
    }
}
