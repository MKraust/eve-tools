<?php

namespace App\Services\DataRefreshment;

use App\Models\CachedOrder;
use Seat\Eseye\Containers\EsiResponse;

class StationsOrdersRefresher extends AbstractOrdersRefresher {

    /** @var int */
    private $_regionId;

    /** @var int[] */
    private $_stationIds;

    public function __construct(int $regionId, array $stationIds) {
        $this->_regionId = $regionId;
        $this->_stationIds = $stationIds;

        $this->_init();
    }

    protected function _getUpdateDataKey(): string {
        return 'stations_market_orders_update_' . $this->_regionId;
    }

    protected function _loadOrders(int $page): EsiResponse {
        return $this->_esi->getMarketOrders($this->_regionId, 'sell', $page);
    }

    protected function _isOrderNeeded($order): bool {
        return in_array($order->location_id, $this->_stationIds);
    }

    protected function _deleteOldData(): void {
        CachedOrder::whereIn('location_id', $this->_stationIds)->delete();
    }
}
