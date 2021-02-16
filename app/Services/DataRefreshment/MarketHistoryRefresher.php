<?php

namespace App\Services\DataRefreshment;

use App\Models\AggregatedPrice;
use App\Models\CachedOrder;
use App\Models\CachedOrdersHistory;
use App\Models\CachedPrice;
use App\Models\Setting;
use App\Services;

class MarketHistoryRefresher {

    /** @var Services\ESI */
    private $_esi;

    private $_regionId;

    public function __construct(int $regionId) {
        $this->_regionId = $regionId;

        $this->_esi = new Services\ESI;

        $marketHistoryUpdateData = Setting::getData($this->_getUpdateDataKey());
        $this->_marketHistoryUpdateData = $marketHistoryUpdateData !== null ? json_decode($marketHistoryUpdateData->value, true) : null;
    }

    public function refresh(): void {
        if ($this->_isInProgress()) {
            info('Already in progress');
            return;
        }

        $this->_initUpdateData();

        try {
            $typeIds = $this->_getTypeIdsForHistoryUpdate();
            $this->_marketHistoryUpdateData['progress']['total_types'] = count($typeIds);
            $this->_saveMarketHistoryUpdateData();

            $this->_clearCachedOrdersHistory($typeIds);

            foreach ($typeIds as $typeId) {
                info("Getting market history for type {$typeId}");

                try {
                    $apiOrdersHistory = $this->_esi->getMarketHistory($this->_regionId, $typeId);
                } catch (\Throwable $t) {
                    if ($t->getMessage() === 'Type not found!') {
                        $this->_incrementProcessedTypesCount();
                        continue;
                    }
                }

                $ordersHistoryData = [];
                foreach ($apiOrdersHistory as $apiOrdersHistoryDatum) {
                    if (new \DateTime($apiOrdersHistoryDatum->date) < now()->modify('-65 day')) {
                        continue;
                    }

                    $ordersHistoryData[] = [
                        'region_id'   => $this->_regionId,
                        'type_id'     => $typeId,
                        'average'     => $apiOrdersHistoryDatum->average,
                        'date'        => $apiOrdersHistoryDatum->date,
                        'highest'     => $apiOrdersHistoryDatum->highest,
                        'lowest'      => $apiOrdersHistoryDatum->lowest,
                        'order_count' => $apiOrdersHistoryDatum->order_count,
                        'volume'      => $apiOrdersHistoryDatum->volume,
                    ];
                }

                CachedOrdersHistory::insert($ordersHistoryData);
                $this->_incrementProcessedTypesCount();
            }

            $this->_finishProcessWithStatus('finished');
        } catch (\Throwable $t) {
            $this->_finishProcessWithStatus('error');

            throw $t;
        }
    }

    private function _isInProgress(): bool {
        return $this->_marketHistoryUpdateData !== null && $this->_marketHistoryUpdateData['status'] === 'in_progress';
    }

    private function _getUpdateDataKey(): string {
        return 'market_history_update_' . $this->_regionId;
    }

    private function _saveMarketHistoryUpdateData(): void {
        Setting::setData($this->_getUpdateDataKey(), json_encode($this->_marketHistoryUpdateData));
    }

    private function _initUpdateData(): void {
        $this->_marketHistoryUpdateData = [
            'status'     => 'in_progress',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date'   => null,
            'progress'   => [
                'is_old_cache_cleared' => false,
                'total_types'          => null,
                'types_processed'      => null,
            ],
        ];

        $this->_saveMarketHistoryUpdateData();
    }

    private function _incrementProcessedTypesCount(): void {
        $this->_marketHistoryUpdateData['progress']['types_processed'] = ($this->_marketHistoryUpdateData['progress']['types_processed'] ?? 0) + 1;
        $this->_saveMarketHistoryUpdateData();
    }

    private function _finishProcessWithStatus(string $status): void {
        $this->_marketHistoryUpdateData['status'] = $status;
        $this->_marketHistoryUpdateData['end_date'] = now()->format('Y-m-d H:i:s');
        $this->_saveMarketHistoryUpdateData();
    }

    /**
     * @return int[]
     */
    private function _getTypeIdsForHistoryUpdate(): array {
        $locationIds = app(Services\Locations\Keeper::class)->getLocationIdsByRegionId($this->_regionId);

        return CachedOrder::selectRaw('distinct(type_id)')
            ->whereIn('location_id', $locationIds)
            ->get()
            ->map->type_id
            ->toArray();
    }

    /**
     * @param int[] $typeIds
     */
    private function _clearCachedOrdersHistory(array $typeIds): void {
        $chunks = array_chunk($typeIds, 500);
        foreach ($chunks as $chunk) {
            CachedOrdersHistory::where('region_id', $this->_regionId)->whereIn('type_id', $chunk)->delete();
        }

        CachedOrdersHistory::where('region_id', $this->_regionId)->where('date', '<', now()->modify('-65 day')->format('Y-m-d'))->delete();

        $this->_marketHistoryUpdateData['progress']['is_old_cache_cleared'] = true;
        $this->_saveMarketHistoryUpdateData();
    }
}
