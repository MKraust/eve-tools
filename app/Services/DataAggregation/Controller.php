<?php

namespace App\Services\DataAggregation;

use App\Services\Locations\Keeper;

class Controller {

    public function aggregatePrices(): void {
        $locationIds = app(Keeper::class)->getLocationsIds();

        foreach ($locationIds as $locationId) {
            $aggregator = new PricesAggregator($locationId);
            $aggregator->aggregate();
        }
    }

    public function aggregateVolumes(): void {
        $regionIds = app(Keeper::class)->getSellingRegionsIds();

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
