<?php

namespace App\Services;

use Seat\Eseye\Eseye;

class ESI {

    /** @var Eseye */
    private $_client;

    public function __construct() {
        $this->_client = new Eseye();
    }

    public function getMarketOrders(int $regionId, string $orderType = 'all', int $page = 1, int $typeId = null) {
        $params = [
            'region_id'  => $regionId,
            'order_type' => $orderType,
            'page'       => $page,
        ];

        if ($typeId !== null) {
            $params['type_id'] = $typeId;
        }

        return $this->_client->invoke('get', '/markets/{region_id}/orders/', $params);
    }
}
