<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;

class TrackedType extends Model
{
    use HasFactory;

    protected $table = 'production_tracked_types';

    protected $fillable = [
        'type_id',
        'production_count',
        'invention_count',
    ];

    protected $with = ['type', 'logs'];

    public function getProducedAttribute() {
        return $this->logs->sum->produced;
    }

    public function getInventedAttribute() {
        return $this->logs->sum->invented;
    }

    public function type() {
        return $this->belongsTo(Models\SDE\Inventory\Type::class, 'type_id', 'typeID');
    }

    public function logs() {
        return $this->hasMany(Models\Production\TrackedTypeLog::class, 'tracked_type_id', 'id');
    }

    public function scopeToday($query) {
        $today = (new \DateTime('today midnight'))->format('Y-m-d H:i:s');
        $tomorrow = (new \DateTime('tomorrow midnight'))->format('Y-m-d H:i:s');

        return $query->where('created_at', '>=', $today)->where('created_at', '<', $tomorrow);
    }
}
