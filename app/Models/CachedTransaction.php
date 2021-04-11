<?php

namespace App\Models;

use App\Models\SDE\Inventory\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'mysql';

    protected $fillable = [
        'character_id',
        'client_id',
        'date',
        'is_buy',
        'is_personal',
        'journal_ref_id',
        'location_id',
        'quantity',
        'transaction_id',
        'type_id',
        'unit_price',
        'processed_quantity',
    ];

    protected $casts = [
        'unit_price' => 'double',
    ];

    public function type() {
        return $this->belongsTo(Type::class, 'type_id', 'typeID');
    }

    public function scopeBuy($query) {
        return $query->where('is_buy', 1);
    }

    public function scopeSell($query) {
        return $query->where('is_buy', 0);
    }

    public function scopeUnprocessed($query) {
        return $query->whereColumn('processed_quantity', '<', 'quantity');
    }

    public function scopeEarliest($query) {
        return $query->orderBy('date', 'asc');
    }

    public function getQuantityToProcessAttribute() {
        return $this->attributes['quantity'] - $this->attributes['processed_quantity'];
    }
}
