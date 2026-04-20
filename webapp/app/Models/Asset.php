<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
    ];

    public function recipeStep()
    {
        return $this->hasMany(RecipeStep::class);
    }

    public function log()
    {
        return $this->hasMany(Log::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }
}
