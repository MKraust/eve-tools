<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'id',
        'name',
        'refresh_token',
        'access_token',
        'expires',
        'esi_scopes',
        'is_data_source',
        'is_trader',
        'is_industrialist',
        'buy_broker_fee_percent',
        'sell_broker_fee_percent',
        'sales_tax_percent',
    ];

    protected $casts = [
        'expires' => 'datetime',
        'esi_scopes'  => 'array',
        'is_data_source' => 'boolean',
        'is_trader' => 'boolean',
        'is_industrialist' => 'boolean',
    ];

    public function scopeDataSource($query) {
        return $query->where('is_data_source', 1);
    }

    public function scopeTrader($query) {
        return $query->where('is_trader', 1);
    }

    public function scopeIndustrialist($query) {
        return $query->where('is_industrialist', 1);
    }
}
