<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedPrice extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'type_id',
        'jita',
        'dichstar',
        'average',
        'adjusted',
    ];
}
