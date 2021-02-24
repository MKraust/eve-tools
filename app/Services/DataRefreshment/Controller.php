<?php

namespace App\Services\DataRefreshment;

use App\Events\MarketHistoryRefreshed;
use App\Events\OrdersRefreshed;
use App\Services\Locations\Keeper;
use App\Services\Locations\Location;

class Controller {

    public function refreshTransactions(): void {
        $characterIds = [
            2117638152, // Jin Kraust
        ];

        foreach ($characterIds as $characterId) {
            $refresher = new TransactionsRefresher($characterId);
            $refresher->refresh();
        }
    }

    public function refreshMarketHistory(): void {
        $regionIds = app(Keeper::class)->getSellingRegionsIds();

        foreach ($regionIds as $regionId) {
            $refresher = new MarketHistoryRefresher($regionId);
            $refresher->refresh();
        }

        MarketHistoryRefreshed::dispatch();
    }

    public function refreshMarketOrders(): void {
        $locationKeeper = app(Keeper::class);

        $structures = $locationKeeper->getStructures();
        $stations = $locationKeeper->getStations()->groupBy(function (Location $location) {
            return $location->regionId();
        });

        foreach ($structures as $structure) {
            $refresher = new StructureOrdersRefresher($structure->id());
            $refresher->refresh();
        }

        foreach ($stations as $regionId => $regionStations) {
            $stationIds = $regionStations->map(function (Location $location) {
                return $location->id();
            })->toArray();

            $refresher = new StationsOrdersRefresher($regionId, $stationIds);
            $refresher->refresh();
        }

        OrdersRefreshed::dispatch();
    }

    public function refreshPrices(): void {
        (new PricesRefresher)->refresh();
    }

    public function refreshIndustryIndices(): void {
        (new IndustryIndicesRefresher)->refresh();
    }

    public function refreshAssets(): void {
        $characterIds = [
            2117638152, // Jin Kraust
        ];

        foreach ($characterIds as $characterId) {
            $refresher = new AssetsRefresher($characterId);
            $refresher->refresh();
        }
    }

    public function refreshContracts(): void {
        $characterIds = [
            2117638152, // Jin Kraust
        ];

        foreach ($characterIds as $characterId) {
            $refresher = new ContractsRefresher($characterId);
            $refresher->refresh();
        }
    }
}
