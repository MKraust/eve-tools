<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SDE\Inventory\Type;
use App\Services;
use App\Services\Locations\Location;
use Illuminate\Http\Request;

class ProductionController extends Controller
{

    private $_sdeRepository;

    private $_productionRepository;

    private $_productionService;

    private $_locationKeeper;

    public function __construct(Services\Locations\Keeper $locationKeeper) {
        $this->_sdeRepository = new Services\SdeRepository;
        $this->_productionRepository = new Services\ProductionRepository;
        $this->_productionService = new Services\ProductionService;

        $this->_locationKeeper = $locationKeeper;
    }

    public function searchModules(Request $request) {
        $request->validate([
            'location_id'  => 'required|integer',
            'search_query' => 'required|string|min:4',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);

        return $this->_sdeRepository
            ->searchRigs($request->search_query)
            ->map(function ($type) use ($location) {
                return $this->_convertTypeToApi($type, $location);
            })
            ->values();
    }

    public function getFavorites(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);

        $favorites = $this->_productionRepository->getFavorites();
        $types = $this->_sdeRepository->getTypesByIds($favorites->map->type_id->toArray(), [
            'techLevelAttribute',
            'blueprint.productionMaterials',
            'blueprint.tech1Blueprint.inventionMaterials',
        ]);

        return $types
            ->sortBy('typeName')
            ->map(function ($type) use ($location) {
                return $this->_convertTypeToApi($type, $location);
            })
            ->values();
    }

    public function getProfitableItems(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);

        return $this->_productionRepository
            ->getProfitableMarketItems($location)
            ->map(function ($type) use ($location) {
                return $this->_convertTypeToApi($type, $location);
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

    public function getTrackedTypes(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);

        return $this->_productionRepository
            ->getTrackedTypes()
            ->map(function ($trackedType) use ($location) {
                return $this->_convertTrackedTypeToApi($trackedType, $location);
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

    private function _convertTypeToApi(Type $type, Location $location) {
        $sellPrice = $type->getSellPrice($location);
        $averageDailyVolume = $type->getAverageDailyVolume($location);

        $productionCost = $this->_productionService->getTypeProductionCost($type);
        $inventionCost = $type->tech_level === 2 ? $this->_productionService->getTypeInventionCost($type) : null;
        $totalCost = $productionCost + ($inventionCost ?? 0);
        $margin = $sellPrice !== null ? $sellPrice * 0.9575 - $totalCost : null;
        $marginPercent = $totalCost > 0 ? round($margin / $totalCost * 100, 2) : 0;
        $potentialDailyProfit = $margin !== null && $averageDailyVolume ? (int)floor($margin * $averageDailyVolume) : null;

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
                'buy'                    => $type->getBuyPrice(),
                'sell'                   => $sellPrice,
                'total_cost'             => $type->getTotalCost($location),
                'margin'                 => $margin,
                'margin_percent'         => $marginPercent,
                'monthly_volume'         => $type->getMonthlyVolume($location),
                'weekly_volume'          => $type->getWeeklyVolume($location),
                'average_daily_volume'   => $averageDailyVolume,
                'potential_daily_profit' => $potentialDailyProfit,
            ],
        ];
    }

    private function _convertTrackedTypeToApi($trackedType, Location $location) {
        return [
            'id'               => $trackedType->id,
            'production_count' => $trackedType->production_count,
            'invention_count'  => $trackedType->invention_count,
            'produced'         => $trackedType->produced,
            'invented'         => $trackedType->invented,
            'date'             => (new \DateTime($trackedType->created_at))->format('Y-m-d'),
            'type'             => $this->_convertTypeToApi($trackedType->type, $location),
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
