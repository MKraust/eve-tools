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

    private function _convertTypeToApi($type) {
        $jitaPrice = $type->jitaPrice;
        $dichstarPrice = $type->dichstarPrice;
        $totalCost = $jitaPrice !== null ? $jitaPrice + $type->volume * 1500 : null;
        $margin = $dichstarPrice !== null && $totalCost !== null ? $dichstarPrice * 0.9575 - $totalCost : null;
        $marginPercent = $totalCost > 0 ? round($margin / $totalCost * 100, 2) : 0;

        return [
            'type_id'    => $type->typeID,
            'name'       => $type->typeName,
            'icon'       => $type->icon,
            'tech_level' => $type->tech_level,
            'volume'     => $type->volume, // TODO: use volume for ships from invVolumes
            'prices'     => [
                'jita'           => $jitaPrice !== null ? (string)$jitaPrice : null,
                'dichstar'       => $dichstarPrice !== null ? (string)$dichstarPrice : null,
                'total_cost'     => $totalCost !== null ? (string)$totalCost : null,
                'margin'         => $margin !== null ? (string)$margin : null,
                'margin_percent' => $marginPercent,
            ],
        ];
    }
}
