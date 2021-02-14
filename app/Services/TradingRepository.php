<?php

namespace App\Services;

use App\Models\AggregatedCharacterOrder;
use App\Models\AggregatedPrice;
use App\Models\AggregatedVolume;
use App\Models\CachedOrder;
use App\Models\CachedPrice;
use App\Models\SDE\Inventory\Type;
use App\Models\Trading;
use App\Services\Locations\Keeper;
use App\Services\Locations\Location;

class TradingRepository {

    private const MIN_POTENTIAL_DAILY_PROFIT = 1000000;

    public function getFavorites() {
        return Trading\Favorite::all();
    }

    public function getFavoriteById(int $typeId) {
        return Trading\Favorite::find($typeId);
    }

    public function addFavorite(int $typeId) {
        $favorite = $this->getFavoriteById($typeId);
        if ($favorite !== null) {
            throw new \Exception('Already exists');
        }

        Trading\Favorite::create([
            'type_id' => $typeId,
        ]);
    }

    public function deleteFavorite(int $typeId) {
        Trading\Favorite::destroy($typeId);
    }

    public function getProfitableMarketItems(Location $location) {
        $locationVolumes = AggregatedVolume::where('region_id', $location->regionId())->where('average_daily', '>', 0)->get();

        $typeIds = $locationVolumes->map->type_id->unique();
        $locationPrices = AggregatedPrice::where('location_id', $location->id())->whereIn('type_id', $typeIds)->whereNotNull('sell')->get();

        $typeIds = $locationPrices->map->type_id->unique();
        $tradingHubIds = app(Keeper::class)->getTradingHubIds();
        $tradingHubPrices = AggregatedPrice::whereIn('location_id', $tradingHubIds)->whereIn('type_id', $typeIds)->whereNotNull('sell')->get();

        $typeIds = $tradingHubPrices->map->type_id->unique();
        $types = Type::whereIn('typeID', $typeIds)->get();

        return $types->filter(function ($type) use ($location) {
           return $type->getPotentialDailyProfit($location) > self::MIN_POTENTIAL_DAILY_PROFIT;
        });
    }

    public function getTraderOrders(int $characterId, int $locationId) {
        return AggregatedCharacterOrder::where('character_id', $characterId)->where('location_id', $locationId)->get();
    }
}
