<?php

namespace App\Services\DataRefreshment;

use App\Events\MarketHistoryRefreshed;
use App\Events\OrdersRefreshed;
use App\Events\StockRefreshed;
use App\Models\Character;
use App\Services\Locations\Keeper;
use App\Services\Locations\Location;

class Controller {

    private const JIN_KRAUST_ID = 2117638152;

    public function refreshTransactions(): void {
        $characterIds = [
            self::JIN_KRAUST_ID, // Jin Kraust
        ];

        foreach ($characterIds as $characterId) {
            $refresher = new TransactionsRefresher($characterId);
            $refresher->refresh();
        }
    }

    public function refreshMarketHistory(): void {
        $regionIds = app(Keeper::class)->getSellingRegionsIds();
        $character = Character::find(self::JIN_KRAUST_ID);

        foreach ($regionIds as $regionId) {
            $refresher = new MarketHistoryRefresher($character, $regionId);
            $refresher->refresh();
        }

        MarketHistoryRefreshed::dispatch();
    }

    public function refreshMarketOrders(): void {
        $locationKeeper = app(Keeper::class);

        $character = Character::find(self::JIN_KRAUST_ID);

        $structures = $locationKeeper->getStructures();
        $stations = $locationKeeper->getStations()->groupBy(function (Location $location) {
            return $location->regionId();
        });

        foreach ($structures as $structure) {
            $refresher = new StructureOrdersRefresher($character, $structure->id());
            $refresher->refresh();
        }

        foreach ($stations as $regionId => $regionStations) {
            $stationIds = $regionStations->map(function (Location $location) {
                return $location->id();
            })->toArray();

            $refresher = new StationsOrdersRefresher($character, $regionId, $stationIds);
            $refresher->refresh();
        }

        OrdersRefreshed::dispatch();
        StockRefreshed::dispatch();
    }

    public function refreshPrices(): void {
        $character = Character::find(self::JIN_KRAUST_ID);
        (new PricesRefresher($character))->refresh();
    }

    public function refreshIndustryIndices(): void {
        $character = Character::find(self::JIN_KRAUST_ID);
        (new IndustryIndicesRefresher($character))->refresh();
    }

    public function refreshAssets(): void {
        $characterIds = [
            self::JIN_KRAUST_ID, // Jin Kraust
        ];

        foreach ($characterIds as $characterId) {
            $character = Character::find($characterId);
            $refresher = new AssetsRefresher($character);
            $refresher->refresh();
        }

        StockRefreshed::dispatch();
    }

    public function refreshContracts(): void {
        $characterIds = [
            self::JIN_KRAUST_ID, // Jin Kraust
        ];

        foreach ($characterIds as $characterId) {
            $refresher = new ContractsRefresher($characterId);
            $refresher->refresh();
        }
    }
}
