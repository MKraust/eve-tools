<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'trading_favorites';
    protected $primaryKey = 'type_id';

    protected $fillable = ['type_id'];
}
