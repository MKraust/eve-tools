<?php

namespace App\Services;

use App\Models\Production;

class TradingRepository {

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
}
