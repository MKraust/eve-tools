<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedAsset extends Model
{
    use HasFactory;

    protected $table = 'cached_assets';

    protected $connection = 'mysql';

    public $timestamps = false;

    protected $fillable = [
        'character_id',
        'is_blueprint_copy',
        'is_singleton',
        'item_id',
        'location_flag',
        'location_id',
        'location_type',
        'quantity',
        'type_id',
    ];
}
