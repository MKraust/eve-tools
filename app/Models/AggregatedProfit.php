<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AggregatedProfit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'mysql';

    protected $fillable = [
        'type_id',
        'quantity',
        'date',
        'margin',
        'delivery_cost',
        'buy_broker_fee',
        'sell_broker_fee',
        'sales_tax',
        'profit',
        'buy_transaction_id',
        'sell_transaction_id',
    ];

    protected $casts = [
        'date'  => 'datetime:Y-m-d H:i:s',
    ];
}
