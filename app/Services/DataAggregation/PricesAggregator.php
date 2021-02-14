<?php


namespace App\Services\DataAggregation;


use App\Models\AggregatedPrice;
use App\Models\CachedOrder;

class PricesAggregator {

    /** @var int */
    private $_locationId;

    public function __construct(int $locationId) {
        $this->_locationId = $locationId;
    }

    public function aggregate(): void {

        $pricesData = CachedOrder::selectRaw('type_id, min(price) as price')->where('location_id', $this->_locationId)->groupBy('type_id')->get();

        $aggregatedPrices = [];
        foreach ($pricesData as $priceData) {
            $aggregatedPrices[] = [
                'type_id' => $priceData->type_id,
                'location_id' => $this->_locationId,
                'sell' => $priceData->price,
                'buy' => null,
            ];
        }

        AggregatedPrice::where('location_id', $this->_locationId)->delete();
        AggregatedPrice::insert($aggregatedPrices);
    }
}
