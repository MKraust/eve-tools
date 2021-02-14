<?php

namespace App\Models;

use App\Models\SDE\Inventory\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AggregatedCharacterOrder extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'mysql';

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_id',
        'location_id',
        'type_id',
        'price',
        'volume_remain',
        'volume_total',
        'outbid',
    ];

    protected $with = ['type'];

    public function type() {
        return $this->belongsTo(Type::class, 'type_id', 'typeID');
    }
}
