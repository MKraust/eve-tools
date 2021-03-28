<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Services\Locations\Keeper;
use App\Services\Locations\Location;

class VueController extends Controller
{
    private Keeper $_locationKeeper;

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

        $characters = Character::all()->map(function (Character $character) {
            return [
                'id' => $character->id,
                'name' => $character->name,
                'roles' => [
                    'data_source'   => $character->is_data_source,
                    'trader'        => $character->is_trader,
                    'industrialist' => $character->is_industrialist,
                ],
            ];
        });

        return view('app', compact('locations', 'characters'));
    }
}
