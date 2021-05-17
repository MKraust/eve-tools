<?php

namespace App\Models;

use App\Models\Manual\Transaction;
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

    protected $appends = [
        'buy_transaction',
    ];

    protected $casts = [
        'date'  => 'datetime:Y-m-d H:i:s',
    ];

    public function type() {
        return $this->belongsTo(Type::class, 'type_id', 'typeID');
    }

    public function cachedBuyTransaction() {
        return $this->belongsTo(CachedTransaction::class, 'buy_transaction_id', 'transaction_id');
    }

    public function manualBuyTransaction() {
        return $this->belongsTo(Transaction::class, 'buy_transaction_id', 'id');
    }

    public function sellTransaction() {
        return $this->belongsTo(CachedTransaction::class, 'sell_transaction_id', 'transaction_id');
    }

    public function getBuyTransactionAttribute() {
        return $this->attributes['buy_transaction_type'] === 'manual' ? $this->manualBuyTransaction : $this->cachedBuyTransaction;
    }
}
