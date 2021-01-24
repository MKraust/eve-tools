<?php

namespace App\Models;

use App\Models\SDE\Inventory\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CachedOrder extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

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

    protected $casts = [
        'price' => 'double',
    ];

    protected $with = [
        'type',
    ];

    public function scopeJita($query) {
        return $query->where('location_id', 60003760);
    }

    public function scopeDichstar($query) {
        return $query->where('location_id', 1031787606461);
    }

    public function scopeMy($query) {
        return $query->where('is_my_order', 1);
    }

    public function competingOrders() {
        return $this->hasMany(static::class, 'type_id', 'type_id')
            ->where('is_my_order', 0);
    }

    public function type() {
        return $this->belongsTo(Type::class, 'type_id', 'typeID');
    }

    public function getOutbiddingOrdersAttribute() {
        return $this->competingOrders->filter(function ($order) {
            return $order->price < $this->price && $order->location_id === $this->location_id;
        });
    }

    public function getIsOutbiddedAttribute() {
        return $this->outbiddingOrders->count() > 0;
    }

    public function getOutbidMarginAttribute(): ?float {
        return $this->isOutBidded ? round($this->outbiddingOrders->map->price->min() - $this->price, 2) : null;
    }

    public function getOutbidMarginPercentAttribute(): ?float {
        $outbidMargin = $this->outbidMargin;

        return $outbidMargin ? round($outbidMargin / $this->price * 100, 2) : null;
    }
}
