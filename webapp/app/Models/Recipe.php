<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
       'name',
    ];

    public function recipeSteps()
    {
        return $this->hasMany(RecipeStep::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
}
