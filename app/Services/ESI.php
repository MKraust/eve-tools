<?php

namespace App\Services;

use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class ESI {

    /** @var Eseye */
    private $_client;

    public function __construct() {
        $authentication = new EsiAuthentication([
            'client_id'     => env('ESI_CLIENT_ID'),
            'secret'        => env('ESI_SECRET_KEY'),
            'refresh_token' => env('ESI_CHARACTER_REFRESH_TOKEN'),
        ]);

        $this->_client = new Eseye($authentication);
    }

    public function getMarketOrders(int $regionId, string $orderType = 'all', int $page = 1, int $typeId = null) {
        $params = [
            'order_type' => $orderType,
            'page'       => $page,
        ];

        if ($typeId !== null) {
            $params['type_id'] = $typeId;
        }

        return $this->_client->setQueryString($params)->invoke('get', '/markets/{region_id}/orders/', ['region_id'  => $regionId]);
    }

    public function getStructureOrders(int $structureId, int $page = 1) {
        return $this->_client
            ->setQueryString(['page' => $page])
            ->invoke('get', '/markets/structures/{structure_id}/', ['structure_id' => $structureId]);
    }
}
