<?php

namespace App\Services;

use App\Models\Production;

class ProductionRepository {

    public function getFavorites() {
        return Production\Favorite::all();
    }

    public function getFavoriteById(int $typeId) {
        return Production\Favorite::find($typeId);
    }

    public function addFavorite(int $typeId) {
        $favorite = $this->getFavoriteById($typeId);
        if ($favorite !== null) {
            throw new \Exception('Already exists');
        }

        Production\Favorite::create([
            'type_id' => $typeId,
        ]);
    }

    public function deleteFavorite(int $typeId) {
        Production\Favorite::destroy($typeId);
    }

    public function getTrackedTypes() {
        return Production\TrackedType::all();
    }

    public function getTrackedTypeById(int $id) {
        return Production\TrackedType::find($id);
    }

    public function getTrackedTypesByIds($ids) {
        return Production\TrackedType::whereIn('id', $ids)->get();
    }

    public function getTodayTrackedTypeByTypeId(int $typeId) {
        return Production\TrackedType::today()->where('type_id', $typeId)->first();
    }

    public function createTrackedType(int $typeId) {
        return Production\TrackedType::create(['type_id' => $typeId]);
    }

    public function deleteTrackedType(int $id) {
        return Production\TrackedType::destroy($id);
    }

    public function createTrackedTypeLog(int $trackedTypeId, int $produced, int $invented) {
        return Production\TrackedTypeLog::create([
            'tracked_type_id' => $trackedTypeId,
            'produced'        => $produced,
            'invented'        => $invented,
        ]);
    }
}
