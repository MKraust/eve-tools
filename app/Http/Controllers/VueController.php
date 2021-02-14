<?php

namespace App\Http\Controllers;

use App\Services\Locations\Keeper;
use App\Services\Locations\Location;

class VueController extends Controller
{
    private $_locationKeeper;

    public function __construct(Keeper $locationKeeper) {
        $this->_locationKeeper = $locationKeeper;
    }

    public function index() {
        $locations = $this->_locationKeeper->getLocations()->map(function (Location $location) {
            return [
                'id' => $location->id(),
                'region_id' => $location->regionId(),
                'name' => $location->name(),
                'is_trading_hub' => $location->isTradingHub(),
            ];
        });

        return view('app', compact('locations'));
    }
}
