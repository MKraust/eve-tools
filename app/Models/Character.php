<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'id',
        'name',
        'refresh_token',
        'access_token',
        'expires',
        'esi_scopes',
    ];

    protected $casts = [
        'expires' => 'datetime',
        'esi_scopes'  => 'array'
    ];
}
