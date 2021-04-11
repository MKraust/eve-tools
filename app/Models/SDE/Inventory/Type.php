<?php

namespace App\Models\SDE\Inventory;

use App\Services\Locations\Keeper;
use App\Services\Locations\Location;
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
        'prices',
        'volumes',
        'stockedItems',
        'deliveredItems',
    ];

    protected $appends = [
        'icon',
    ];

    public function getVolumeAttribute(): ?float {
        return $this->attributes['volume'] == 4000 ? 1000 : $this->attributes['volume'];
    }

    public function getTechLevelAttribute(): ?int {
        return $this->techLevelAttribute ? $this->techLevelAttribute->valueFloat : null;
    }

    public function getIconAttribute() {
        return "https://imageserver.eveonline.com/Type/{$this->typeID}_64.png";
    }

    public function getJitaPriceAttribute(): ?float {
        return $this->price->jita ?? null;
    }

    public function getDichstarPriceAttribute(): ?float {
        return $this->price->dichstar ?? null;
    }

    public function getAdjustedPriceAttribute(): ?float {
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

    public function prices() {
        return $this->hasMany(Models\AggregatedPrice::class, 'type_id', 'typeID');
    }

    public function volumes() {
        return $this->hasMany(Models\AggregatedVolume::class, 'type_id', 'typeID');
    }

    public function stockedItems() {
        return $this->hasMany(Models\AggregatedStockedItem::class, 'type_id', 'typeID');
    }

    public function deliveredItems() {
        return $this->hasMany(Models\DeliveredItem::class, 'type_id', 'typeID');
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




    public function getStockedQuantity(Location $location, int $characterId): int {
        $stock = $this->stockedItems->first(function (Models\AggregatedStockedItem $stockedItem) use ($location, $characterId) {
            return $stockedItem->location_id === $location->id() && $stockedItem->character_id === $characterId;
        });

        return $stock ? $stock->in_hangar + $stock->in_market : 0;
    }

    public function getDeliveredQuantity(Location $location): int {
        $deliveredItems = $this->deliveredItems->filter(function (Models\DeliveredItem $deliveredItem) use ($location) {
            return $deliveredItem->delivery->finished_at === null
                && $deliveredItem->delivery->destination_id === $location->id();
        });

        return $deliveredItems->count() > 0 ? $deliveredItems->sum('quantity') : 0;
    }

    public function getDeliveryCost(Location $location): float {
        return $this->volume * $location->deliveryCost();
    }

    public function getTotalCost(Location $location): ?float {
        $buyPrice = $this->getBuyPrice();

        return $buyPrice !== null ? $buyPrice + $this->volume * $location->deliveryCost() : null;
    }

    public function getMargin(Location $location): ?float {
        $totalCost = $this->getTotalCost($location);
        $sellPrice = $this->getSellPrice($location);

        return $sellPrice !== null && $totalCost !== null ? round($sellPrice * 0.9575 - $totalCost, 2) : null;
    }

    public function getBuyPrice() {
        $locationsKeeper = app(Keeper::class);

        return $this->prices->filter(function ($price) use ($locationsKeeper) {
            return in_array($price->location_id, $locationsKeeper->getTradingHubIds());
        })->min('sell'); // buy from sell orders
    }

    public function getSellPrice(Location $location) {
        return $this->prices->first(function ($price) use ($location) {
                return $price->location_id === $location->id();
            })->sell ?? null;
    }

    public function getMarginPercent(Location $location): ?float {
        $totalCost = $this->getTotalCost($location);

        return $totalCost > 0 ? round($this->getMargin($location) / $totalCost * 100, 2) : 0;
    }

    public function getPotentialDailyProfit(Location $location): ?int {
        $averageDailyVolume = $this->getAverageDailyVolume($location);
        $margin = $this->getMargin($location);

        return $margin !== null && $averageDailyVolume ? floor($margin * $averageDailyVolume) : null;
    }

    public function getMonthlyVolume(Location $location): ?int {
        $volume = $this->volumes->first(function (Models\AggregatedVolume $volume) use ($location) {
            return $volume->region_id === $location->regionId();
        });

        return $volume->monthly ?? null;
    }

    public function getWeeklyVolume(Location $location): ?int {
        $volume = $this->volumes->first(function (Models\AggregatedVolume $volume) use ($location) {
            return $volume->region_id === $location->regionId();
        });

        return $volume->weekly ?? null;
    }

    public function getAverageDailyVolume(Location $location): ?float {
        $volume = $this->volumes->first(function (Models\AggregatedVolume $volume) use ($location) {
            return $volume->region_id === $location->regionId();
        });

        return $volume->average_daily ?? null;
    }
}
