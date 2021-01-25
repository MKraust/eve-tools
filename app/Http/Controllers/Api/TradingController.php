<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CachedOrder;
use App\Services;
use Illuminate\Filesystem\Cache;
use Illuminate\Http\Request;

class TradingController extends Controller
{

    private $_sdeRepository;

    private $_tradingRepository;

    private $_esi;

    public function __construct() {
        $this->_sdeRepository = new Services\SdeRepository;
        $this->_tradingRepository = new Services\TradingRepository;
        $this->_esi = new Services\ESI;
    }

    public function getOrders() {
        return $this->_tradingRepository
            ->getTraderOrders()
            ->map(function ($order) {
                return $this->_convertOrderToApi($order);
            })
            ->values();
    }

    public function getProfitableItems() {
        return $this->_tradingRepository
            ->getProfitableMarketItems()
            ->map(function ($type) {
                return $this->_convertTypeToApi($type);
            })
            ->values();
    }

    public function searchModules(Request $request) {
        $request->validate([
            'search_query' => 'required|string|min:4',
        ]);

        return $this->_sdeRepository
            ->searchTypes($request->search_query)
            ->map(function ($type) {
                return $this->_convertTypeToApi($type);
            })
            ->values();
    }

    public function getFavorites() {
        $favorites = $this->_tradingRepository->getFavorites();
        $types = $this->_sdeRepository->getTypesByIds($favorites->map->type_id->toArray());

        return $types
            ->sortBy('typeName')
            ->map(function ($type) {
                return $this->_convertTypeToApi($type);
            })
            ->values();
    }

    public function addFavorite(Request $request) {
        $request->validate([
            'type_id' => 'required|integer|exists:App\Models\SDE\Inventory\Type,typeID',
        ]);

        $type = $this->_sdeRepository->getTypeById($request->type_id);

        try {
            $this->_tradingRepository->addFavorite($request->type_id);
        } catch (\Exception $e) {
            return response('Already exists', 400);
        }

        return $this->_convertTypeToApi($type);
    }

    public function deleteFavorite(Request $request) {
        $request->validate([
            'type_id' => 'required|integer',
        ]);

        $this->_tradingRepository->deleteFavorite($request->type_id);

        return ['status' => 'success'];
    }

    public function openMarketDetails(Request $request) {
        $request->validate([
            'type_id' => 'required|integer',
        ]);

        $this->_esi->openMarketDetailsWindow($request->type_id);

        return ['status' => 'success'];
    }

    private function _convertTypeToApi($type) {
        return [
            'type_id'    => $type->typeID,
            'name'       => $type->typeName,
            'icon'       => $type->icon,
            'volume'     => $type->volume, // TODO: use volume for ships from invVolumes
            'prices'     => [
                'jita'                   => $type->jitaPrice,
                'dichstar'               => $type->dichstarPrice,
                'total_cost'             => $type->totalCost,
                'margin'                 => $type->margin,
                'margin_percent'         => $type->marginPercent,
                'monthly_volume'         => $type->monthlyVolume,
                'weekly_volume'          => $type->weeklyVolume,
                'average_daily_volume'   => $type->averageDailyVolume,
                'potential_daily_profit' => $type->potentialDailyProfit,
            ],
        ];
    }

    private function _convertOrderToApi($order) {
        return [
          'order_id'              => $order->order_id,
          'price'                 => $order->price,
          'volume_remain'         => $order->volume_remain,
          'volume_total'          => $order->volume_total,
          'type'                  => $this->_convertTypeToApi($order->type),
          'is_outbidded'          => $order->isOutbidded,
          'outbid_margin'         => $order->outbidMargin,
          'outbid_margin_percent' => $order->outbidMarginPercent,
        ];
    }
}
