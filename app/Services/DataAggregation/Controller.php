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
        $characters = Character::trader()->get();

        foreach ($characters as $character) {
            $aggregator = new CharacterOrdersAggregator($character);
            $aggregator->aggregate();
        }
    }

    public function aggregateStockedItems(): void {
        $characters = Character::trader()->get();
        $locations = app(Keeper::class)->getSellingLocations();

        foreach ($characters as $character) {
            foreach ($locations as $location) {
                $aggregator = new StockedItemsAggregator($character, $location);
                $aggregator->aggregate();
            }
        }
    }

    public function aggregateProfits(): void {
        (new ProfitsAggregator)->aggregate();
    }
}
