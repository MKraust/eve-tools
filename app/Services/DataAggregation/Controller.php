<?php

namespace App\Services\DataAggregation;

class Controller {

    public function aggregatePrices(): void {
        $locationIds = [
            1031787606461, // DICHSTAR
            60003760, // Jita 4-4
        ];

        foreach ($locationIds as $locationId) {
            $aggregator = new PricesAggregator($locationId);
            $aggregator->aggregate();
        }
    }

    public function aggregateVolumes(): void {
        $regionIds = [
            10000009, // Insmother
        ];

        foreach ($regionIds as $regionId) {
            $aggregator = new VolumesAggregator($regionId);
            $aggregator->aggregate();
        }
    }

    public function aggregateCharactersOrders(): void {
        $characterIds = [
            2117638152, // Jin Kraust
        ];

        foreach ($characterIds as $characterId) {
            $aggregator = new CharacterOrdersAggregator($characterId);
            $aggregator->aggregate();
        }
    }
}
