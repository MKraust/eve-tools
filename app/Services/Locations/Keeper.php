<?php

namespace App\Services\Locations;

use Illuminate\Support\Collection;

class Keeper {

    private $_locations;

    public function __construct() {
        $locationsData = config('locations', []);
        $locations = [];
        foreach ($locationsData as $locationData) {
            $locations[] = new Location(
                $locationData['location_id'],
                $locationData['name'],
                $locationData['region_id'],
                $locationData['is_structure'],
                $locationData['is_trading_hub'],
                $locationData['delivery_cost']
            );
        }

        $this->_locations = collect($locations);
    }

    public function getLocations(): Collection {
        return $this->_locations;
    }

    public function getStructures(): Collection {
        return $this->_locations->filter(function (Location $location) {
            return $location->isStructure();
        });
    }

    public function getStations(): Collection {
        return $this->_locations->filter(function (Location $location) {
            return !$location->isStructure();
        });
    }

    public function getById(int $id): ?Location {
        return $this->_locations->first(function (Location $location) use ($id) {
            return $location->id() === $id;
        });
    }

    public function getTradingHubs(): Collection {
        return $this->_locations->filter(function (Location $location) {
            return $location->isTradingHub();
        });
    }

    /**
     * @return int[]
     */
    public function getTradingHubIds(): array {
        return $this->getTradingHubs()->map(function (Location $location) {
            return $location->id();
        })->toArray();
    }

    public function getSellingLocations(): Collection {
        return $this->_locations->filter(function (Location $location) {
            return !$location->isTradingHub();
        });
    }

    public function getSellingLocationsIds(): array {
        return $this->getSellingLocations()->map(function (Location $location) {
            return $location->id();
        })->toArray();
    }

    public function getSellingRegionsIds(): array {
        return $this->getSellingLocations()->map(function (Location $location) {
            return $location->regionId();
        })
        ->unique()
        ->toArray();
    }

    public function getLocationsIds(): array {
        return $this->_locations->map(function (Location $location) {
            return $location->id();
        })->toArray();
    }
}
