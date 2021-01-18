<?php

namespace App\Jobs;

use App\Models\CachedOrdersHistory;
use App\Models\CachedPrice;
use App\Models\Setting;
use App\Services;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshMarketHistory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const INSMOTHER_REGION_ID = 10000009;

    private $_esi;

    private $_marketHistoryUpdateData;

    public function __construct()
    {
        $this->_esi = new Services\ESI;

        $marketHistoryUpdateData = Setting::getData('market_history_update_data');
        $this->_marketHistoryUpdateData = $marketHistoryUpdateData !== null ? json_decode($marketHistoryUpdateData->value, true) : null;
    }

    public function handle()
    {
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

        try {
            $typeIds = $this->_getTypeIdsForHistoryUpdate();
            $this->_marketHistoryUpdateData['progress']['total_types'] = count($typeIds);
            $this->_saveMarketHistoryUpdateData();

            $this->_clearCachedOrdersHistory($typeIds);
            $this->_marketHistoryUpdateData['progress']['is_old_cache_cleared'] = true;
            $this->_saveMarketHistoryUpdateData();

            $ordersHistoryData = [];
            foreach ($typeIds as $typeId) {
                Log::info("Getting market history for type {$typeId}");

                try {
                    $apiOrdersHistory = $this->_esi->getMarketHistory(self::INSMOTHER_REGION_ID, $typeId);
                } catch (\Throwable $t) {
                    if ($t->getMessage() === 'Type not found!') {
                        $this->_marketHistoryUpdateData['progress']['types_processed'] = ($this->_marketHistoryUpdateData['progress']['types_processed'] ?? 0) + 1;
                        $this->_saveMarketHistoryUpdateData();
                        continue;
                    }
                }

                foreach ($apiOrdersHistory as $apiOrdersHistoryDatum) {
                    if (new \DateTime($apiOrdersHistoryDatum->date) < now()->modify('-31 day')) {
                        continue;
                    }

                    $ordersHistoryData[] = [
                        'type_id'     => $typeId,
                        'average'     => $apiOrdersHistoryDatum->average,
                        'date'        => $apiOrdersHistoryDatum->date,
                        'highest'     => $apiOrdersHistoryDatum->highest,
                        'lowest'      => $apiOrdersHistoryDatum->lowest,
                        'order_count' => $apiOrdersHistoryDatum->order_count,
                        'volume'      => $apiOrdersHistoryDatum->volume,
                    ];
                }

                $this->_marketHistoryUpdateData['progress']['types_processed'] = ($this->_marketHistoryUpdateData['progress']['types_processed'] ?? 0) + 1;
                $this->_saveMarketHistoryUpdateData();
            }

            $chunks = array_chunk(array_values($ordersHistoryData), 500);
            foreach ($chunks as $chunk) {
                CachedOrdersHistory::insert($chunk);
            }

            $this->_marketHistoryUpdateData['status'] = 'finished';
            $this->_marketHistoryUpdateData['end_date'] = now()->format('Y-m-d H:i:s');
            $this->_saveMarketHistoryUpdateData();
        } catch (\Throwable $t) {
            $this->_marketHistoryUpdateData['status'] = 'error';
            $this->_marketHistoryUpdateData['end_date'] = now()->format('Y-m-d H:i:s');
            $this->_saveMarketHistoryUpdateData();

            throw $t;
        }
    }

    private function _getTypeIdsForHistoryUpdate() {
        return CachedPrice::select('type_id')
            ->whereNotNull('dichstar')
            ->get()
            ->map
            ->type_id
            ->toArray();
    }

    private function _clearCachedOrdersHistory(array $typeIds) {
        $chunks = array_chunk($typeIds, 500);
        foreach ($chunks as $chunk) {
            CachedOrdersHistory::whereIn('type_id', $chunk)->delete();
        }

        CachedOrdersHistory::where('date', '<', now()->modify('-31 day')->format('Y-m-d'))->delete();
    }

    private function _saveMarketHistoryUpdateData() {
        Setting::setData('market_history_update_data', json_encode($this->_marketHistoryUpdateData));
    }
}
