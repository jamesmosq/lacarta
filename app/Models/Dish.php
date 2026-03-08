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
}
