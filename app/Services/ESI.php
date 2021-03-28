<?php

namespace App\Services;

use App\Models\Character;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiConfiguration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class ESI {

    private Eseye $_client;

    private Character $_character;

    public function __construct(Character $character) {
        $config = Configuration::getInstance();
        $config->setConfiguration(new EsiConfiguration([
            'http_user_agent'     => config('esi.user_agent'),
            'logfile_location'    => storage_path() . (php_sapi_name() === 'cli' ? '/cli' : '') . '/logs',
            'file_cache_location' => storage_path() . (php_sapi_name() === 'cli' ? '/cli' : '') . '/esi',
        ]));

        $authentication = new EsiAuthentication([
            'client_id'     => config('esi.client_id'),
            'secret'        => config('esi.secret'),
            'refresh_token' => $character->refresh_token,
        ]);

        $this->_client = new Eseye($authentication);
        $this->_character = $character;
    }

    public function getMarketOrders(int $regionId, string $orderType = 'all', int $page = 1, int $typeId = null) {
        $params = [
            'order_type' => $orderType,
            'page'       => $page,
        ];

        if ($typeId !== null) {
            $params['type_id'] = $typeId;
        }

        return $this->_client
            ->setQueryString($params)
            ->invoke('get', '/markets/{region_id}/orders/', ['region_id'  => $regionId]);
    }

    public function getStructureOrders(int $structureId, int $page = 1) {
        return $this->_client
            ->setQueryString(['page' => $page])
            ->invoke('get', '/markets/structures/{structure_id}/', ['structure_id' => $structureId]);
    }

    public function getIndustrySystems() {
        return $this->_client->invoke('get', '/industry/systems/');
    }

    public function getMarketPrices() {
        return $this->_client->invoke('get', '/markets/prices/');
    }

    public function getMarketHistory(int $regionId, int $typeId) {
        return $this->_client
            ->setQueryString(['type_id' => $typeId])
            ->invoke('get', '/markets/{region_id}/history', ['region_id' => $regionId]);
    }

    public function getCharacterOrders(int $characterId) {
        return $this->_client->invoke('get', '/characters/{character_id}/orders', ['character_id' => $characterId]);
    }

    public function openMarketDetailsWindow(int $typeId) {
        $this->_client->setQueryString(['type_id' => $typeId])->invoke('post', '/ui/openwindow/marketdetails/');
    }

    public function getWalletTransactions(int $characterId, ?int $fromId = null) {
        if ($fromId !== null) {
            $this->_client->setQueryString(['from_id' => $fromId]);
        }

        return $this->_client->invoke('get', '/characters/{character_id}/wallet/transactions/', [
            'character_id' => $characterId,
        ]);
    }

    public function getCharacterAssets(int $characterId, int $page = 1) {
        return $this->_client
            ->setQueryString(['page' => $page])
            ->invoke('get', '/characters/{character_id}/assets/', [
                'character_id' => $characterId,
            ]);
    }

    public function getCharacterContracts(int $characterId, int $page = 1) {
        return $this->_client
            ->setQueryString(['page' => $page])
            ->invoke('get', '/characters/{character_id}/contracts/', [
                'character_id' => $characterId,
            ]);
    }

    public function getContractItems(int $characterId, int $contractId) {
        return $this->_client->invoke('get', '/characters/{character_id}/contracts/{contract_id}/items/', [
            'character_id' => $characterId,
            'contract_id'  => $contractId,
        ]);
    }
}
