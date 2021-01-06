<?php

namespace App\Services;

use App\Models\Trading;

class TradingRepository {

    public function getFavorites() {
        return Trading\Favorite::all();
    }

    public function getFavoriteById(int $typeId) {
        return Trading\Favorite::find($typeId);
    }

    public function addFavorite(int $typeId) {
        $favorite = $this->getFavoriteById($typeId);
        if ($favorite !== null) {
            throw new \Exception('Already exists');
        }

        Trading\Favorite::create([
            'type_id' => $typeId,
        ]);
    }

    public function deleteFavorite(int $typeId) {
        Trading\Favorite::destroy($typeId);
    }
}
