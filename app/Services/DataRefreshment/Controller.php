<?php

namespace App\Services\DataRefreshment;

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
        $regionIds = [
            10000009, // Insmother
        ];

        foreach ($regionIds as $regionId) {
            $refresher = new MarketHistoryRefresher($regionId);
            $refresher->refresh();
        }
    }

    public function refreshMarketOrders(): void {
        $structureIds = [
            1031787606461, // DICHSTAR
        ];

        $stations = [
            10000002 => [ // The Forge
                60003760, // Jita 4-4
            ],
        ];

        foreach ($structureIds as $structureId) {
            $refresher = new StructureOrdersRefresher($structureId);
            $refresher->refresh();
        }

        foreach ($stations as $regionId => $stationIds) {
            $refresher = new StationsOrdersRefresher($regionId, $stationIds);
            $refresher->refresh();
        }
    }

    public function refreshPrices(): void {
        (new PricesRefresher)->refresh();
    }
}
