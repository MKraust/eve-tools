<?php

namespace App\Models;

use App\Models\SDE\Inventory\Type;
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

    public function type() {
        return $this->belongsTo(Type::class, 'type_id', 'typeID');
    }

    public function buyTransaction() {
        return $this->belongsTo(CachedTransaction::class, 'buy_transaction_id', 'transaction_id');
    }

    public function sellTransaction() {
        return $this->belongsTo(CachedTransaction::class, 'sell_transaction_id', 'transaction_id');
    }
}
