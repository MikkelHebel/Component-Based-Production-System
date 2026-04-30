<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
       'name',
    ];

    public function recipeSteps(): HasMany
    {
        return $this->hasMany(RecipeStep::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }
}
