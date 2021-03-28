<?php

namespace App\Services\DataAggregation;

use App\Models\AggregatedCharacterOrder;
use App\Models\CachedOrder;
use App\Models\Character;
use App\Services\ESI;

class CharacterOrdersAggregator {

    private Character $_character;

    /** @var ESI */
    private $_esi;

    public function __construct(Character $character) {
        $this->_character = $character;
        $this->_esi = new ESI($character);
    }

    public function aggregate(): void {
        $orders = $this->_esi->getCharacterOrders($this->_character->id);
        $orderIds = collect($orders->getArrayCopy())->map->order_id->toArray();

        $cachedOrders = CachedOrder::whereIn('order_id', $orderIds)->get();
        $aggregatedOrdersData = [];
        foreach ($cachedOrders as $cachedOrder) {
            $minPrice = CachedOrder::selectRaw('min(price) as price')
                ->where('type_id', $cachedOrder->type_id)
                ->where('location_id', $cachedOrder->location_id)
                ->whereNotIn('order_id', $orderIds)
                ->where('price', '<', $cachedOrder->price)
                ->first();

            $aggregatedOrdersData[] = [
                'character_id' => $this->_character->id,
                'order_id' => $cachedOrder->order_id,
                'location_id' => $cachedOrder->location_id,
                'type_id' => $cachedOrder->type_id,
                'price' => $cachedOrder->price,
                'volume_remain' => $cachedOrder->volume_remain,
                'volume_total' => $cachedOrder->volume_total,
                'outbid' => $minPrice && $minPrice->price ? round($cachedOrder->price - $minPrice->price, 2) : null,
            ];
        }

        AggregatedCharacterOrder::where('character_id', $this->_character->id)->delete();
        AggregatedCharacterOrder::insert($aggregatedOrdersData);
    }
}
