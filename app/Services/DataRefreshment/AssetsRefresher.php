<?php

namespace App\Services\DataRefreshment;

use App\Models\CachedAsset;
use App\Models\Character;
use App\Services;
use Seat\Eseye\Containers\EsiResponse;

class AssetsRefresher {

    private Services\ESI $_esi;

    private Character $_character;

    public function __construct(Character $character) {
        $this->_character = $character;
        $this->_esi = new Services\ESI($character);
    }

    public function refresh(): void {
        $this->_clearAssets();

        $page = 1;

        do {
            $assets = retry(4, function () use ($page) {
                return $this->_esi->getCharacterAssets($this->_character->id, $page);
            }, 1000);

            $this->_storeAssets($assets->getArrayCopy());

            $page++;
        } while ($page <= $assets->pages);
    }

    private function _clearAssets(): void {
        CachedAsset::where('character_id', $this->_character->id)->delete();
    }

    private function _storeAssets(array $assets): void {
        $assetsData = array_map(function ($asset) {
            $a = [];
            $a['is_blueprint_copy'] = false;
            $a = array_merge($a, json_decode(json_encode($asset), true));
            $a['character_id'] = $this->_character->id;

            return $a;
        }, $assets);

        CachedAsset::insert($assetsData);
    }
}
