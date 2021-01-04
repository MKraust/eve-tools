<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services;
use Illuminate\Http\Request;

class ProductionController extends Controller
{

    private $_sdeRepository;

    private $_productionRepository;

    public function __construct() {
        $this->_sdeRepository = new Services\SdeRepository;
        $this->_productionRepository = new Services\ProductionRepository;
    }

    public function searchModules(Request $request) {
        $request->validate([
            'search_query' => 'required|string|min:4',
        ]);

        return $this->_sdeRepository
            ->searchModules($request->search_query)
            ->map(function ($rig) {
                return [
                    'id'   => $rig->typeID,
                    'name' => $rig->typeName,
                    'icon' => $rig->icon,
                ];
            })
            ->values();
    }

    public function getFavorites() {
        $favorites = $this->_productionRepository->getFavorites();
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
            'type_id' => 'required|integer',
        ]);

        $type = $this->_sdeRepository->getTypeById($request->type_id);
        if ($type === null) {
            return response('Type not found', 400);
        }

        try {
            $this->_productionRepository->addFavorite($request->type_id);
        } catch (\Exception $e) {
            return response('Already exists', 400);
        }

        return $this->_convertTypeToApi($type);
    }

    public function deleteFavorite(Request $request) {
        $request->validate([
            'type_id' => 'required|integer',
        ]);

        $this->_productionRepository->deleteFavorite($request->type_id);

        return ['status' => 'success'];
    }

    private function _convertTypeToApi($type) {
        return [
            'id'   => $type->typeID,
            'name' => $type->typeName,
            'icon' => $type->icon,
        ];
    }
}
