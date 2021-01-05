<?php

namespace App\Models\SDE\Dogma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeAttribute extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $connection = 'sde';
    protected $table = 'dgmTypeAttributes';
    protected $primaryKey = 'typeID';
}
