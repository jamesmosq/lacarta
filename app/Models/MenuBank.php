<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuBank extends Model
{
    protected $connection = 'pgsql_central';
    protected $table = 'menu_bank';

    protected $fillable = ['name', 'description', 'default_price', 'category_hint'];
}
