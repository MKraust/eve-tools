<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedPrice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'mysql';

    protected $primaryKey = 'type_id';

    protected $fillable = [
        'type_id',
        'average',
        'adjusted',
    ];
}
