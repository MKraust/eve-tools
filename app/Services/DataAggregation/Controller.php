<?php

namespace App\Services\DataAggregation;

use App\Models\Character;
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
            $character = Character::find($characterId);
            $aggregator = new CharacterOrdersAggregator($character);
            $aggregator->aggregate();
        }
    }

    public function aggregateStockedItems(): void {
        $characterIds = [
            2117638152, // Jin Kraust
        ];

        $locations = app(Keeper::class)->getSellingLocations();

        foreach ($characterIds as $characterId) {
            foreach ($locations as $location) {
                $aggregator = new StockedItemsAggregator($characterId, $location);
                $aggregator->aggregate();
            }
        }
    }
}
