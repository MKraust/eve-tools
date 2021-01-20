<?php

namespace App\Models\SDE\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;

class Type extends Model
{
    use HasFactory;

    private const DELIVERY_COST_PER_M3 = 1500;

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

    public function getJitaPriceAttribute(): ?float {
        return $this->price->jita ?? null;
    }

    public function getTotalCostAttribute(): ?float {
        $jitaPrice = $this->jitaPrice;

        return $jitaPrice !== null ? $jitaPrice + $this->volume * self::DELIVERY_COST_PER_M3 : null;
    }

    public function getMarginAttribute(): ?float {
        $totalCost = $this->totalCost;
        $dichstarPrice = $this->dichstarPrice;

        return $dichstarPrice !== null && $totalCost !== null ? round($dichstarPrice * 0.9575 - $totalCost, 2) : null;
    }

    public function getMarginPercentAttribute(): ?float {
        $totalCost = $this->totalCost;

        return $totalCost > 0 ? round($this->margin / $totalCost * 100, 2) : 0;
    }

    public function getPotentialDailyProfitAttribute(): ?float {
        $averageDailyVolume = $this->averageDailyVolume;
        $margin = $this->margin;

        return $margin !== null && $averageDailyVolume ? round($margin * $averageDailyVolume, 2) : null;
    }

    public function getDichstarPriceAttribute(): ?float {
        return $this->price->dichstar ?? null;
    }

    public function getAdjustedPriceAttribute(): ?float {
        return $this->price->adjusted ?? null;
    }

    public function getMonthlyVolumeAttribute(): ?int {
        return $this->price->monthly_volume ?? null;
    }

    public function getWeeklyVolumeAttribute(): ?int {
        return $this->price->weekly_volume ?? null;
    }

    public function getAverageDailyVolumeAttribute(): ?float {
        return $this->price->average_daily_volume ?? null;
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
