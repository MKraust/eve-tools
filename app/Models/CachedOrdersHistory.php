<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedOrdersHistory extends Model
{
    use HasFactory;

    protected $table = 'cached_orders_history';

    protected $connection = 'mysql';

    public $timestamps = false;

    protected $fillable = [
        'type_id',
        'average',
        'date',
        'highest',
        'lowest',
        'order_count',
        'volume',
    ];
}
