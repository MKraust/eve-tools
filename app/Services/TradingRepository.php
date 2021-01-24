<?php

namespace App\Services;

use App\Models\CachedOrder;
use App\Models\CachedPrice;
use App\Models\SDE\Inventory\Type;
use App\Models\Trading;

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

    public function getProfitableMarketItems() {
        $typeIds = CachedPrice::whereNotNull('jita')
            ->whereNotNull('dichstar')
            ->whereNotNull('average_daily_volume')
            ->get()
            ->map->type_id->unique();

        $types = Type::whereIn('typeID', $typeIds)->get();

        return $types->filter(function ($type) {
           return $type->potentialDailyProfit > self::MIN_POTENTIAL_DAILY_PROFIT;
        });
    }

    public function getTraderOrders() {
        return CachedOrder::my()->with(['competingOrders'])->get();
    }
}
