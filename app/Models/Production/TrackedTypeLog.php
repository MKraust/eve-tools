<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackedTypeLog extends Model
{
    use HasFactory;

    protected $table = 'production_tracked_type_logs';

    protected $fillable = [
        'tracked_type_id',
        'produced',
        'invented',
    ];
}
