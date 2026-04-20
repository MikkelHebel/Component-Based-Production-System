<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'status',
        'quantity',
        'priority',
    ];

    public function log()
    {
        return $this->hasMany(Log::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function batchRecipeStep()
    {
        return $this->hasMany(BatchRecipeStep::class);
    }
}
