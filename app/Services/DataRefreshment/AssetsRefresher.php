<?php

namespace App\Services\DataRefreshment;

use App\Models\CachedAsset;
use App\Services;
use Seat\Eseye\Containers\EsiResponse;

class AssetsRefresher {

    /** @var Services\ESI */
    private $_esi;

    /** @var int */
    private $_characterId;

    public function __construct(int $characterId) {
        $this->_esi = new Services\ESI;

        $this->_characterId = $characterId;
    }

    public function refresh(): void {
        $this->_clearAssets();

        $page = 1;

        do {
            $assets = retry(4, function () use ($page) {
                return $this->_esi->getCharacterAssets($this->_characterId, $page);
            }, 1000);

            $this->_storeAssets($assets->getArrayCopy());

            $page++;
        } while ($page <= $assets->pages);
    }

    private function _clearAssets(): void {
        CachedAsset::where('character_id', $this->_characterId)->delete();
    }

    private function _storeAssets(array $assets): void {
        $assetsData = array_map(function ($asset) {
            $a = json_decode(json_encode($asset), true);
            $a['character_id'] = $this->_characterId;

            return $a;
        }, $assets);

        CachedAsset::insert($assetsData);
    }
}
