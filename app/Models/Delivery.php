<?php

namespace App\Models;

use App\Models\SDE\Inventory\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'destination_id',
        'finished_at',
    ];

    protected $with = [

    ];

    public function items() {
        return $this->hasMany(DeliveredItem::class);
    }
}
