<?php

namespace App\Models;

use App\Models\SDE\Inventory\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveredItem extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'type_id',
        'quantity',
        'volume',
        'delivery_id',
    ];

    protected $with = [
        'delivery',
    ];

    public function type() {
        return $this->belongsTo(Type::class, 'type_id', 'typeID');
    }

    public function delivery() {
        return $this->belongsTo(Delivery::class);
    }
}
