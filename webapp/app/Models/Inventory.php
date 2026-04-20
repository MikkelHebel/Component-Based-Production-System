<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'tray_number',
        'quantity',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
