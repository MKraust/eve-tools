<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration',
        'is_buy_order',
        'issued',
        'location_id',
        'min_volume',
        'order_id',
        'price',
        'range',
        'system_id',
        'type_id',
        'volume_remain',
        'volume_total',
    ];

    public function scopeJita($query) {
        return $query->where('location_id', 60003760);
    }

    public function scopeDichstar($query) {
        return $query->where('location_id', 1031787606461);
    }
}
