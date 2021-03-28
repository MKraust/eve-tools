<?php

namespace App\Services\DataRefreshment;

use App\Models\CachedPrice;
use App\Models\Character;
use App\Services\ESI;
use Illuminate\Support\Facades\DB;

class PricesRefresher {

    /** @var ESI */
    private $_esi;

    public function __construct(Character $character) {
        $this->_esi = new ESI($character);
    }

    public function refresh(): void {
        $apiPrices = $this->_esi->getMarketPrices();

        DB::table('cached_prices')->truncate();

        $pricesData = [];
        foreach ($apiPrices as $price) {
            $pricesData[] = [
                'type_id'  => $price->type_id,
                'average'  => $price->average_price ?? null,
                'adjusted' => $price->adjusted_price ?? null,
            ];
        }

        CachedPrice::insert($pricesData);
    }
}
