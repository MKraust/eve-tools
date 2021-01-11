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
        'price',
    ];

    protected $casts = [
        'volume' => 'double',
    ];

    public function getTechLevelAttribute(): ?int {
        return $this->techLevelAttribute ? $this->techLevelAttribute->valueFloat : null;
    }

    public function getIconAttribute() {
        return "https://imageserver.eveonline.com/Type/{$this->typeID}_64.png";
    }

    public function getJitaPriceAttribute() {
        return $this->price->jita ?? null;
    }

    public function getDichstarPriceAttribute() {
        return $this->price->dichstar ?? null;
    }

    public function getAdjustedPriceAttribute() {
        return $this->price->adjusted ?? null;
    }

    public function getBlueprintProductionMaterialsAttribute() {
        return $this->blueprint->productionMaterials;
    }

    public function getBlueprintInventionMaterialsAttribute() {
        return $this->techLevel === 2 ? $this->blueprint->tech1Blueprint->inventionMaterials : [];
    }

    public function blueprint() {
        return $this->hasOneThrough(self::class, Models\SDE\Industry\ActivityProduct::class, 'productTypeID', 'typeID', 'typeID', 'typeID')
                    ->where('activityID', 1);
    }

    public function tech1Blueprint() {
        return $this->hasOneThrough(self::class, Models\SDE\Industry\ActivityProduct::class, 'productTypeID', 'typeID', 'typeID', 'typeID')
                    ->where('activityID', 8);
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

    public function price() {
        return $this->hasOne(Models\CachedPrice::class, 'type_id', 'typeID');
    }

    public function scopeRigs($query) {
        return $query->whereIn('groupID', [773, 774, 775, 776, 777, 778, 779, 781, 782, 786, 896, 904, 1232, 1233, 1234, 1308]);
    }

    public function _getJitaOrders() {
        return $this->jitaOrders->sortBy('price');
    }

    public function _getDichstarOrders() {
        return $this->dichstarOrders->sortBy('price');
    }
}
