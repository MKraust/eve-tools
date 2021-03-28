<?php

namespace App\Services\DataRefreshment;

use App\Models\CachedOrder;
use App\Models\Character;
use Seat\Eseye\Containers\EsiResponse;

class StructureOrdersRefresher extends AbstractOrdersRefresher {

    /** @var int */
    private $_structureId;

    public function __construct(Character $character, int $structureId) {
        $this->_structureId = $structureId;

        $this->_init($character);
    }

    protected function _getUpdateDataKey(): string {
        return 'structure_market_orders_update_' . $this->_structureId;
    }

    protected function _loadOrders(int $page): EsiResponse {
        return $this->_esi->getStructureOrders($this->_structureId, $page);
    }

    protected function _isOrderNeeded($order): bool {
        return !$order->is_buy_order;
    }

    protected function _deleteOldData(): void {
        CachedOrder::where('location_id', $this->_structureId)->delete();
    }
}
