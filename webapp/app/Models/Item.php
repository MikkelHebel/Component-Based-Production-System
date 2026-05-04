<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
