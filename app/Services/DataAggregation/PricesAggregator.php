<?php


namespace App\Services\DataAggregation;


use App\Models\AggregatedPrice;
use App\Models\CachedOrder;
use App\Services\ESI;

class PricesAggregator {

    /** @var int */
    private $_locationId;

    public function __construct(int $locationId) {
        $this->_locationId = $locationId;
    }

    public function aggregate(): void {
        AggregatedPrice::where('location_id', $this->_locationId)->delete();

        $pricesData = CachedOrder::select('type_id, min(price) as price')->where('location_id', $this->_locationId)->groupBy('type_id')->get();

        $aggregatedPrices = [];
        foreach ($pricesData as $priceData) {
            $aggregatedPrices[] = [
                'type_id' => $priceData->type_id,
                'location_id' => $this->_locationId,
                'sell' => $priceData->price,
                'buy' => null,
            ];
        }

        AggregatedPrice::insert($aggregatedPrices);
    }
}
