<?php

namespace App\Services\DataAggregation;

use App\Models\AggregatedCharacterOrder;
use App\Models\AggregatedStockedItem;
use App\Models\CachedAsset;
use App\Models\CachedOrder;
use App\Models\Character;
use App\Services\ESI;
use App\Services\Locations\Location;

class StockedItemsAggregator {

    private Character $_character;

    /** @var Location */
    private $_location;

    /** @var ESI */
    private $_esi;

    public function __construct(Character $character, Location $location) {
        $this->_character = $character;
        $this->_location = $location;

        $this->_esi = new ESI($character);
    }

    public function aggregate(): void {
        $assets = CachedAsset::selectRaw('type_id, SUM(quantity) as quantity')
            ->where('character_id', $this->_character->id)
            ->where('location_id', $this->_location->id())
            ->where('is_singleton', 0)
            ->groupBy('type_id')
            ->get();

        $results = [];
        foreach ($assets as $asset) {
            $results[$asset->type_id] = [
                'character_id' => $this->_character->id,
                'location_id'  => $this->_location->id(),
                'type_id'      => $asset->type_id,
                'in_hangar'    => $asset->quantity,
                'in_market'    => 0,
            ];
        }

        $orders = AggregatedCharacterOrder::selectRaw('type_id, SUM(volume_remain) as volume_remain')
            ->where('character_id', $this->_character->id)
            ->where('location_id', $this->_location->id())
            ->groupBy('type_id')
            ->get();

        foreach ($orders as $order) {
            $result = $results[$order->type_id] ?? [
                'character_id' => $this->_character->id,
                'location_id'  => $this->_location->id(),
                'type_id'      => $order->type_id,
                'in_hangar'    => 0,
                'in_market'    => 0,
            ];

            $result['in_market'] += $order->volume_remain;
            $results[$order->type_id] = $result;
        }

        AggregatedStockedItem::where('character_id', $this->_character->id)
            ->where('location_id', $this->_location->id())
            ->delete();

        AggregatedStockedItem::insert(array_values($results));
    }
}
