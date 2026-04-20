<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function recipeItem()
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }
}
