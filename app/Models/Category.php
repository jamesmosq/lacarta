<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'image', 'order', 'active'];

    public function dishes()
    {
        return $this->hasMany(Dish::class)->orderBy('order');
    }

    public function activeDishes()
    {
        return $this->hasMany(Dish::class)->where('available', true)->orderBy('order');
    }
}
