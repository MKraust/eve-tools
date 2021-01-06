<?php

namespace App\Models\SDE\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;

class Type extends Model
{
    use HasFactory;

    protected $connection = 'sde';
    protected $table = 'invTypes';
    protected $primaryKey = 'typeID';

    protected $with = [
        'techLevelAttribute',
    ];

    public function blueprint() {
        return $this->hasOneThrough(self::class, Models\SDE\Industry\ActivityProduct::class, 'productTypeID', 'typeID', 'typeID', 'typeID')
                    ->where('activityID', 1);
    }

    public function tech1Blueprint() {
        return $this->hasOneThrough(self::class, Models\SDE\Industry\ActivityProduct::class, 'productTypeID', 'typeID', 'typeID', 'typeID')
                    ->where('activityID', 8);
    }

    public function getTechLevelAttribute(): ?int {
        return $this->techLevelAttribute ? $this->techLevelAttribute->valueFloat : null;
    }

    public function getIconAttribute() {
        return $this->iconID !== null ? "https://images.evetech.net/types/{$this->typeID}/icon" : null;
    }

    public function productionMaterials() {
        return $this->hasMany(Models\SDE\Industry\ActivityMaterial::class, 'typeID', 'typeID')
                    ->where('activityID', 1);
    }

    public function inventionMaterials() {
        return $this->hasMany(Models\SDE\Industry\ActivityMaterial::class, 'typeID', 'typeID')
                    ->where('activityID', 8);
    }

    public function techLevelAttribute() {
        return $this->hasOne(Models\SDE\Dogma\TypeAttribute::class, 'typeID', 'typeID')
                    ->where('attributeID', 422);
    }

    public function scopeRigs($query) {
        return $query->whereIn('groupID', [773, 774, 775, 776, 777, 778, 779, 781, 782, 786, 896, 904, 1232, 1233, 1234, 1308]);
    }
}
