<?php

namespace App\Services\DataRefreshment;

use App\Services;
use App\Models;

class IndustryIndicesRefresher {

    private const INDUSTRY_SOLAR_SYSTEM_ID = 30000498; // KZ9T-C

    /** @var Services\ESI */
    private $_esi;

    public function __construct(Models\Character $character) {
        $this->_esi = new Services\ESI($character);
    }

    public function refresh() {
        $industrySystems = $this->_esi->getIndustrySystems();
        $dichstarSystem = collect($industrySystems)->first(function ($system) {
            return $system->solar_system_id === self::INDUSTRY_SOLAR_SYSTEM_ID;
        });

        Models\Setting::setData('industry_indices', json_encode($dichstarSystem->cost_indices));
    }
}
