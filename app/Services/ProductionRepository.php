<?php

namespace App\Services;

use App\Models\AggregatedPrice;
use App\Models\AggregatedVolume;
use App\Models\CachedPrice;
use App\Models\Production;
use App\Models\SDE\Inventory\Type;
use App\Services\Locations\Keeper;
use App\Services\Locations\Location;

class ProductionRepository {

    private const MIN_POTENTIAL_DAILY_PROFIT = 1000000;

    public function getFavorites() {
        return Production\Favorite::all();
    }

    public function getFavoriteById(int $typeId) {
        return Production\Favorite::find($typeId);
    }

    public function addFavorite(int $typeId) {
        $favorite = $this->getFavoriteById($typeId);
        if ($favorite !== null) {
            throw new \Exception('Already exists');
        }

        Production\Favorite::create([
            'type_id' => $typeId,
        ]);
    }

    public function deleteFavorite(int $typeId) {
        Production\Favorite::destroy($typeId);
    }

    public function getTrackedTypes() {
        return Production\TrackedType::all();
    }

    public function getTrackedTypeById(int $id) {
        return Production\TrackedType::find($id);
    }

    public function getTrackedTypesByIds($ids) {
        return Production\TrackedType::whereIn('id', $ids)->get();
    }

    public function getTodayTrackedTypeByTypeId(int $typeId) {
        return Production\TrackedType::today()->where('type_id', $typeId)->first();
    }

    public function createTrackedType(int $typeId) {
        return Production\TrackedType::create(['type_id' => $typeId]);
    }

    public function deleteTrackedType(int $id) {
        return Production\TrackedType::destroy($id);
    }

    public function createTrackedTypeLog(int $trackedTypeId, int $produced, int $invented) {
        return Production\TrackedTypeLog::create([
            'tracked_type_id' => $trackedTypeId,
            'produced'        => $produced,
            'invented'        => $invented,
        ]);
    }

    public function getProfitableMarketItems(Location $location) {
        $locationVolumes = AggregatedVolume::where('region_id', $location->regionId())->where('average_daily', '>', 0)->get();
        $typeIds = $locationVolumes->map->type_id->unique();

        $locationPrices = AggregatedPrice::where('location_id', $location->id())->whereIn('type_id', $typeIds)->whereNotNull('sell')->get();
        $typeIds = $locationPrices->map->type_id->unique();

        return Type::rigs()->whereIn('typeID', $typeIds)->with([
            'techLevelAttribute',
            'blueprint.productionMaterials',
            'blueprint.tech1Blueprint.inventionMaterials',
        ])->get();
    }
}
