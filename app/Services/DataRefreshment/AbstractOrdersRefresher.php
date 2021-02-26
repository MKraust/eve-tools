<?php

namespace App\Services\DataRefreshment;

use App\Models\CachedOrder;
use App\Models\Setting;
use App\Services\ESI;
use Illuminate\Support\Facades\Log;
use Seat\Eseye\Containers\EsiResponse;

abstract class AbstractOrdersRefresher {

    /** @var ESI */
    protected $_esi;

    /** @var array|null */
    protected $_marketOrdersUpdateData;

    protected function _init(): void {
        $this->_esi = new ESI;

        $settings = Setting::getData($this->_getUpdateDataKey());
        $this->_marketOrdersUpdateData = $settings !== null ? json_decode($settings->value, true) : null;
    }

    abstract protected function _getUpdateDataKey(): string;

    abstract protected function _loadOrders(int $page): EsiResponse;

    abstract protected function _isOrderNeeded($order): bool;

    abstract protected function _deleteOldData(): void;

    public function refresh(): void {
//        if ($this->_isInProgress()) {
//            logger('Already in progress');
//            return;
//        }

        $this->_initUpdateData();

        try {
            $this->_clearCachedOrders();
            $this->_refreshOrders();
        } catch (\Throwable $t) {
            $this->_finishProcessWithStatus('error');

            throw $t;
        }

        $this->_finishProcessWithStatus('finished');
    }

    private function _clearCachedOrders(): void {
        $this->_deleteOldData();

        $this->_marketOrdersUpdateData['progress']['is_table_cleared'] = true;
        $this->_saveMarketOrdersUpdateData();
    }

    private function _refreshOrders(): void {
        $page = 1;

        do {
            $orders = retry(4, function () use ($page) {
                return $this->_loadOrders($page);
            }, 1000);

            logger('Request succeeded');

            $ordersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return $this->_isOrderNeeded($order);
            });

            logger('Start saving orders');
            $this->_storeOrders($ordersCollection->toArray());
            logger('FInish saving orders');

            $this->_marketOrdersUpdateData['progress']['total_pages'] = $orders->pages;
            $this->_marketOrdersUpdateData['progress']['processed_pages'] = $page;
            $this->_saveMarketOrdersUpdateData();

            logger("Processed Jita page {$page}");

            $page++;
        } while ($page <= $orders->pages);
    }

    private function _storeOrders($orders): void {
        $ordersData = array_map(function ($order) {
            $o = json_decode(json_encode($order), true);
            $o['issued'] = (new \DateTime($o['issued']))->format('Y-m-d H:i:s');

            return $o;
        }, $orders);

        $chunks = array_chunk($ordersData, 250);

        foreach ($chunks as $chunk) {
            CachedOrder::insert($chunk);
        }
    }

    private function _isInProgress(): bool {
        return $this->_marketOrdersUpdateData !== null && $this->_marketOrdersUpdateData['status'] === 'in_progress';
    }

    private function _saveMarketOrdersUpdateData(): void {
        Setting::setData($this->_getUpdateDataKey(), json_encode($this->_marketOrdersUpdateData));
    }

    private function _initUpdateData(): void {
        $this->_marketOrdersUpdateData = [
            'start_date' => (new \DateTime)->format('Y-m-d H:i:s'),
            'end_date'   => null,
            'status'     => 'in_progress',
            'progress'   => [
                'is_table_cleared'   => false,
                'total_pages'     => null,
                'processed_pages' => 0,
            ],
        ];

        $this->_saveMarketOrdersUpdateData();
    }

    private function _finishProcessWithStatus(string $status): void {
        $this->_marketOrdersUpdateData['status'] = $status;
        $this->_marketOrdersUpdateData['end_date'] = now()->format('Y-m-d H:i:s');
        $this->_saveMarketOrdersUpdateData();
    }
}
