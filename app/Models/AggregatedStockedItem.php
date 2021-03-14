<?php

namespace App\Models;

use App\Models\SDE\Inventory\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AggregatedStockedItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'mysql';

    protected $fillable = [
        'character_id',
        'type_id',
        'location_id',
        'in_hangar',
        'in_market',
    ];

    public function type() {
        return $this->belongsTo(Type::class, 'type_id', 'typeID');
    }
}
