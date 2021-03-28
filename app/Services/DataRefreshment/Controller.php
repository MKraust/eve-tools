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
        $characters = Character::trader()->get();

        foreach ($characters as $character) {
            $refresher = new TransactionsRefresher($character);
            $refresher->refresh();
        }
    }

    public function refreshMarketHistory(): void {
        $regionIds = app(Keeper::class)->getSellingRegionsIds();
        $character = Character::dataSource()->first();

        foreach ($regionIds as $regionId) {
            $refresher = new MarketHistoryRefresher($character, $regionId);
            $refresher->refresh();
        }

        MarketHistoryRefreshed::dispatch();
    }

    public function refreshMarketOrders(): void {
        $locationKeeper = app(Keeper::class);

        $character = Character::dataSource()->first();

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
        $character = Character::dataSource()->first();
        (new PricesRefresher($character))->refresh();
    }

    public function refreshIndustryIndices(): void {
        $character = Character::dataSource()->first();
        (new IndustryIndicesRefresher($character))->refresh();
    }

    public function refreshAssets(): void {
        $characters = Character::all();

        foreach ($characters as $character) {
            $refresher = new AssetsRefresher($character);
            $refresher->refresh();
        }

        StockRefreshed::dispatch();
    }

    public function refreshContracts(): void {
        $characters = Character::trader()->get();

        foreach ($characters as $character) {
            $refresher = new ContractsRefresher($character);
            $refresher->refresh();
        }
    }
}
