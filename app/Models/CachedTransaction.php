<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'mysql';

    protected $fillable = [
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
    ];

    protected $casts = [
        'unit_price' => 'double',
    ];
}
