<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedContractItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'cached_contract_items';

    protected $connection = 'mysql';

    protected $primaryKey = 'record_id';

    protected $fillable = [
        'contract_id',
        'is_included',
        'is_singleton',
        'quantity',
        'raw_quantity',
        'record_id',
        'type_id',
    ];
}
