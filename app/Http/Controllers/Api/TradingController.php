<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services;
use Illuminate\Http\Request;

class TradingController extends Controller
{

    private $_sdeRepository;

    private $_tradingRepository;

    public function __construct() {
        $this->_sdeRepository = new Services\SdeRepository;
        $this->_tradingRepository = new Services\TradingRepository;
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
        return [
            'type_id'    => $type->typeID,
            'name'       => $type->typeName,
            'icon'       => $type->icon,
            'tech_level' => $type->tech_level,
        ];
    }
}
