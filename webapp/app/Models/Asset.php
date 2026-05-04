<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'name',
    ];

    public function recipeSteps(): HasMany
    {
        return $this->hasMany(RecipeStep::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
