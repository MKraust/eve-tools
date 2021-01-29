<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services;
use Illuminate\Http\Request;

class ProductionController extends Controller
{

    private $_sdeRepository;

    private $_productionRepository;

    private $_productionService;

    public function __construct() {
        $this->_sdeRepository = new Services\SdeRepository;
        $this->_productionRepository = new Services\ProductionRepository;
        $this->_productionService = new Services\ProductionService;
    }

    public function searchModules(Request $request) {
        $request->validate([
            'search_query' => 'required|string|min:4',
        ]);

        return $this->_sdeRepository
            ->searchRigs($request->search_query)
            ->map(function ($type) {
                return $this->_convertTypeToApi($type);
            })
            ->values();
    }

    public function getFavorites() {
        $favorites = $this->_productionRepository->getFavorites();
        $types = $this->_sdeRepository->getTypesByIds($favorites->map->type_id->toArray(), [
            'techLevelAttribute',
            'blueprint.productionMaterials',
            'blueprint.tech1Blueprint.inventionMaterials',
        ]);

        return $types
            ->sortBy('typeName')
            ->map(function ($type) {
                return $this->_convertTypeToApi($type);
            })
            ->values();
    }

    public function getProfitableItems() {
        return $this->_productionRepository
            ->getProfitableMarketItems()
            ->map(function ($type) {
                return $this->_convertTypeToApi($type);
            })
            ->filter(function ($apiType) {
                return $apiType['prices']['potential_daily_profit'] > 1000000;
            })
            ->values();
    }

    public function addFavorite(Request $request) {
        $request->validate([
            'type_id' => 'required|integer|exists:App\Models\SDE\Inventory\Type,typeID',
        ]);

        $type = $this->_sdeRepository->getTypeById($request->type_id);

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

    public function getTrackedTypes() {
        return $this->_productionRepository
            ->getTrackedTypes()
            ->map(function ($trackedType) {
                return $this->_convertTrackedTypeToApi($trackedType);
            })
            ->values();
    }

    public function addTrackedType(Request $request) {
        $request->validate([
            'type_id'          => 'required|integer|exists:App\Models\SDE\Inventory\Type,typeID',
            'production_count' => 'required|integer|min:1',
            'invention_count'  => 'required|integer|min:0',
        ]);

        $trackedType = $this->_productionRepository->getTodayTrackedTypeByTypeId($request->type_id)
                    ?? $this->_productionRepository->createTrackedType($request->type_id);

        $trackedType->production_count += $request->production_count;
        $trackedType->invention_count += $request->invention_count;
        $trackedType->save();

        return ['status' => 'success'];
    }

    public function deleteTrackedType(Request $request) {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $this->_productionRepository->deleteTrackedType($request->id);

        return ['status' => 'success'];
    }

    public function editTrackedType(Request $request) {
        $request->validate([
            'id'               => 'required|integer|exists:App\Models\Production\TrackedType',
            'production_count' => 'required|integer|min:1',
            'invention_count'  => 'required|integer|min:0',
        ]);

        $trackedType = $this->_productionRepository->getTrackedTypeById($request->id);
        $trackedType->production_count = $request->production_count;
        $trackedType->invention_count = $request->invention_count;
        $trackedType->save();

        return ['status' => 'success'];
    }

    public function updateTrackedTypeDoneCounts(Request $request) {
        $request->validate([
            'id'        => 'required|integer|exists:App\Models\Production\TrackedType',
            'produced'  => 'required|integer|min:0',
            'invented'  => 'required|integer|min:0',
        ]);

        $trackedTypeLog = $this->_productionRepository->createTrackedTypeLog($request->id, $request->produced, $request->invented);

        return $this->_convertTrackedTypeLogToApi($trackedTypeLog);
    }

    public function getShoppingList(Request $request) {
        $request->validate([
            'tracked_type_ids'   => 'required|array|min:1',
        ]);

        $trackedTypes = $this->_productionRepository->getTrackedTypesByIds($request->tracked_type_ids);

        $materialsQuantityByName = [];
        foreach ($trackedTypes as $trackedType) {
            $productionMaterials = $trackedType->type->blueprintProductionMaterials;
            foreach ($productionMaterials as $productionMaterial) {
                $materialTypeName = $productionMaterial->materialType->typeName;
                $quantity = ($materialsQuantityByName[$materialTypeName] ?? 0) + $productionMaterial->quantity * (max($trackedType->production_count - $trackedType->produced, 0));
                $materialsQuantityByName[$materialTypeName] = $quantity;
            }

            if ($trackedType->type->tech_level === 2) {
                $inventionMaterials = $trackedType->type->blueprintInventionMaterials;
                foreach ($inventionMaterials as $inventionMaterial) {
                    $materialTypeName = $inventionMaterial->materialType->typeName;
                    $quantity = ($materialsQuantityByName[$materialTypeName] ?? 0) + $inventionMaterial->quantity * (max($trackedType->invention_count - $trackedType->invented, 0));
                    $materialsQuantityByName[$materialTypeName] = $quantity;
                }
            }
        }

        return array_filter($materialsQuantityByName);
    }

    private function _convertTypeToApi($type) {
        $productionCost = $this->_productionService->getTypeProductionCost($type);
        $inventionCost = $type->tech_level === 2 ? $this->_productionService->getTypeInventionCost($type) : null;
        $totalCost = $productionCost + ($inventionCost ?? 0);
        $margin = $type->dichstarPrice !== null ? $type->dichstarPrice * 0.9575 - $totalCost : null;
        $marginPercent = $totalCost > 0 ? round($margin / $totalCost * 100, 2) : 0;
        $potentialDailyProfit = $margin !== null && $type->averageDailyVolume ? (int)floor($margin * $type->averageDailyVolume) : null;

        return [
            'type_id'    => $type->typeID,
            'name'       => $type->typeName,
            'icon'       => $type->icon,
            'tech_level' => $type->tech_level,
            'costs'      => [
                'production' => $productionCost,
                'invention'  => $inventionCost !== null ? (string)$inventionCost : null,
                'total'      => $totalCost,
            ],
            'prices'     => [
                'jita'                   => $type->jitaPrice,
                'dichstar'               => $type->dichstarPrice,
                'total_cost'             => $type->totalCost,
                'margin'                 => $margin,
                'margin_percent'         => $marginPercent,
                'monthly_volume'         => $type->monthlyVolume,
                'weekly_volume'          => $type->weeklyVolume,
                'average_daily_volume'   => $type->averageDailyVolume,
                'potential_daily_profit' => $potentialDailyProfit,
            ],
        ];
    }

    private function _convertTrackedTypeToApi($trackedType) {
        return [
            'id'               => $trackedType->id,
            'production_count' => $trackedType->production_count,
            'invention_count'  => $trackedType->invention_count,
            'produced'         => $trackedType->produced,
            'invented'         => $trackedType->invented,
            'date'             => (new \DateTime($trackedType->created_at))->format('Y-m-d'),
            'type'             => $this->_convertTypeToApi($trackedType->type),
        ];
    }

    private function _convertTrackedTypeLogToApi($trackedTypeLog) {
        return [
            'id'              => $trackedTypeLog->id,
            'tracked_type_id' => $trackedTypeLog->tracked_type_id,
            'produced'        => $trackedTypeLog->produced,
            'invented'        => $trackedTypeLog->invented,
        ];
    }
}
