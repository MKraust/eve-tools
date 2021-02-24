<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedContract extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'cached_contracts';

    protected $connection = 'mysql';

    protected $primaryKey = 'contract_id';

    protected $fillable = [
        'character_id',
        'acceptor_id',
        'assignee_id',
        'availability',
        'buyout',
        'collateral',
        'contract_id',
        'date_accepted',
        'date_completed',
        'date_expired',
        'date_issued',
        'days_to_complete',
        'end_location_id',
        'for_corporation',
        'issuer_corporation_id',
        'issuer_id',
        'price',
        'reward',
        'start_location_id',
        'status',
        'title',
        'type',
        'volume',
    ];

    protected $casts = [
        'date_accepted'  => 'datetime:Y-m-d H:i:s',
        'date_completed' => 'datetime:Y-m-d H:i:s',
        'date_expired'   => 'datetime:Y-m-d H:i:s',
        'date_issued'    => 'datetime:Y-m-d H:i:s',
    ];
}
