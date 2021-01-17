<?php

namespace App\Jobs;

use App\Models\CachedOrdersHistory;
use App\Models\CachedPrice;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services;
use App\Models\CachedOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshMarketData implements ShouldQueue
{
    private const THE_FORGE_REGION_ID = 10000002;
    private const JITA_TRADING_HUB_ID = 60003760;
    private const DICHSTAR_SOLAR_SYSTEM_ID = 30000843;
    private const DICHSTAR_STRUCTURE_ID = 1031787606461;
    private const INSMOTHER_REGION_ID = 10000009;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $_esi;

    private $_settings;

    private $_marketHistoryUpdateData;

    private $_minJitaPrices = [];

    private $_minDichstarPrices = [];

    private $_pricesData = [];

    public function __construct()
    {
        $this->_esi = new Services\ESI;

        $settings = Setting::getData('market_orders_update');
        $this->_settings = $settings !== null ? json_decode($settings->value, true) : null;

        $marketHistoryUpdateData = Setting::getData('market_history_update_data');
        $this->_marketHistoryUpdateData = $marketHistoryUpdateData !== null ? json_decode($marketHistoryUpdateData->value, true) : null;
    }

    public function handle()
    {
        $status = $this->_settings !== null && $this->_settings['status'] === 'in_progress';
        if ($status) {
            return;
        }

        $this->_settings = [
            'start_date' => (new \DateTime)->format('Y-m-d H:i:s'),
            'end_date'   => null,
            'status'     => 'in_progress',
            'progress'   => [
                'is_table_cleared'   => false,
                'is_prices_updated'  => false,
                'is_volumes_updated' => false,
                'jita'               => [
                    'total_pages'     => null,
                    'processed_pages' => 0,
                ],
                'dichstar'           => [
                    'total_pages'     => null,
                    'processed_pages' => 0,
                ],
            ],
        ];
        $this->_saveSettings();

        try {
            $this->_clearCachedOrders();

            $this->_refreshDichstarOrders();
            $this->_refreshJitaOrders();

            if ($this->_isNeedToRefreshOrdersHistory()) {
                $this->_refreshOrdersHistory();
            }

            $this->_refreshPrices();
            $this->_refreshSystemIndustryIndices();
        } catch (\Throwable $t) {
            $this->_settings['status'] = 'error';
            $this->_saveSettings();

            Log::info($t->getMessage());
            Log::info($t->getTraceAsString());

            throw $t;
        }

        $this->_settings['status'] = 'finished';
        $this->_settings['end_date'] = (new \DateTime)->format('Y-m-d H:i:s');
        $this->_saveSettings();
    }

    private function _isNeedToRefreshOrdersHistory() {
        return $this->_marketHistoryUpdateData !== null
            ? new \DateTime($this->_marketHistoryUpdateData['end_date']) <= (new \DateTime('now'))->modify('-1 day')
            : true;
    }

    private function _refreshOrdersHistory() {
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
            $typeIds = array_keys($this->_minDichstarPrices);
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

    private function _clearCachedOrdersHistory(array $typeIds) {
        $chunks = array_chunk($typeIds, 500);
        foreach ($chunks as $chunk) {
            CachedOrdersHistory::whereIn('type_id', $chunk)->delete();
        }

        CachedOrdersHistory::where('date', '<', now()->modify('-31 day')->format('Y-m-d'))->delete();
    }

    private function _refreshSystemIndustryIndices() {
        $industrySystems = $this->_esi->getIndustrySystems();
        $dichstarSystem = collect($industrySystems)->first(function ($system) {
            return $system->solar_system_id === self::DICHSTAR_SOLAR_SYSTEM_ID;
        });

        Setting::setData('industry_indices', json_encode($dichstarSystem->cost_indices));
    }

    private function _refreshPrices() {
        $this->_aggregateJitaPrices();
        $this->_aggregateDichstarPrices();
        $this->_aggregateAverageAndAdjustedPrices();
        $this->_settings['progress']['is_prices_updated'] = true;
        $this->_saveSettings();

        $this->_aggregateVolumes();
        $this->_settings['progress']['is_volumes_updated'] = true;

        DB::table('cached_prices')->truncate();

        $chunks = array_chunk(array_values($this->_pricesData), 500);
        foreach ($chunks as $chunk) {
            CachedPrice::insert($chunk);
        }
    }

    private function _aggregateJitaPrices() {
        foreach ($this->_minJitaPrices as $typeId => $price) {
            $priceData = $this->_pricesData[$typeId] ?? $this->_getEmptyPricesData($typeId);

            $priceData['jita'] = $price;
            $this->_pricesData[$typeId] = $priceData;
        }
    }

    private function _aggregateDichstarPrices() {
        foreach ($this->_minDichstarPrices as $typeId => $price) {
            $priceData = $this->_pricesData[$typeId] ?? $this->_getEmptyPricesData($typeId);
            $priceData['dichstar'] = $price;

            $this->_pricesData[$typeId] = $priceData;
        }
    }

    private function _aggregateAverageAndAdjustedPrices() {
        $apiPrices = $this->_esi->getMarketPrices();
        foreach ($apiPrices as $price) {
            $priceData = $this->_pricesData[$price->type_id] ?? $this->_getEmptyPricesData($price->type_id);
            $priceData['average'] = $price->average_price ?? null;
            $priceData['adjusted'] = $price->adjusted_price ?? null;

            $this->_pricesData[$price->type_id] = $priceData;
        }
    }

    private function _aggregateVolumes() {
        $typeIds = array_keys($this->_minDichstarPrices);
        $typeIdsChunks = array_chunk($typeIds, 100);
        foreach ($typeIdsChunks as $typeIdsChunk) {
            $monthAgo = now()->modify('-30 days')->format('Y-m-d');
            $ordersHistoryData = CachedOrdersHistory::whereIn('type_id', $typeIdsChunk)->where('date', '>=', $monthAgo)->get();

            foreach ($ordersHistoryData->groupBy('type_id') as $typeId => $typeOrdersHistoryData) {
                $priceData = $this->_pricesData[$typeId] ?? $this->_getEmptyPricesData($typeId);

                $monthlyVolume = $typeOrdersHistoryData->sum('volume');
                $priceData['monthly_volume'] = $monthlyVolume;
                $priceData['weekly_volume'] = $typeOrdersHistoryData->filter(function ($historyDatum) {
                    return new \DateTime($historyDatum->date) >= now()->modify('-7 days');
                })->sum->volume;
                $priceData['average_daily_volume'] = round($monthlyVolume / 30, 2);

                $this->_pricesData[$typeId] = $priceData;
            }
        }
    }

    private function _getEmptyPricesData(int $typeId) {
        return [
            'type_id' => $typeId,
            'jita' => null,
            'dichstar' => null,
            'average' => null,
            'adjusted' => null,
            'monthly_volume' => null,
            'weekly_volume' => null,
            'average_daily_volume' => null,
        ];
    }

    private function _clearCachedOrders() {
        DB::table('cached_orders')->truncate();

        $this->_settings['progress']['is_table_cleared'] = true;
        $this->_saveSettings();
    }

    private function _refreshJitaOrders() {
        $page = 1;
        do {
            $this->_logMemoryUsage();

            $orders = $this->_retry(function () use ($page) {
               return $this->_esi->getMarketOrders(self::THE_FORGE_REGION_ID, 'sell', $page);
            }, 4);
            Log::info('Request succeeded');

            $ordersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return $order->location_id === self::JITA_TRADING_HUB_ID;
            });

            foreach ($ordersCollection as $order) {
                $this->_minJitaPrices[$order->type_id] = array_key_exists($order->type_id, $this->_minJitaPrices)
                    ? min($this->_minJitaPrices[$order->type_id], $order->price)
                    : $order->price;
            }

            Log::info('Start saving orders');
            $this->_storeOrders($ordersCollection->toArray());
            Log::info('FInish saving orders');

            $this->_settings['progress']['jita']['total_pages'] = $orders->pages;
            $this->_settings['progress']['jita']['processed_pages'] = $page;
            $this->_saveSettings();
            Log::info("Processed Jita page {$page}");
            $page++;
        } while ($page <= $orders->pages);
    }

    private function _refreshDichstarOrders() {
        $page = 1;
        do {
            $this->_logMemoryUsage();

            $orders = $this->_retry(function () use ($page) {
                return $this->_esi->getStructureOrders(self::DICHSTAR_STRUCTURE_ID, $page);
            }, 4);
            Log::info('Request succeeded');

            $ordersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return !$order->is_buy_order;
            });

            foreach ($ordersCollection as $order) {
                $this->_minDichstarPrices[$order->type_id] = array_key_exists($order->type_id, $this->_minDichstarPrices)
                    ? min($this->_minDichstarPrices[$order->type_id], $order->price)
                    : $order->price;
            }

            Log::info('Start saving orders');
            $this->_storeOrders($ordersCollection->toArray());
            Log::info('FInish saving orders');

            $this->_settings['progress']['dichstar']['total_pages'] = $orders->pages;
            $this->_settings['progress']['dichstar']['processed_pages'] = $page;
            $this->_saveSettings();
            Log::info("Processed Dichstar page {$page}");
            $page++;
        } while ($page <= $orders->pages);
    }

    private function _storeOrders($orders) {
        Log::info('Start formatting orders');
        $ordersData = array_map(function ($order) {
            $o = json_decode(json_encode($order), true);
            $o['issued'] = (new \DateTime($o['issued']))->format('Y-m-d H:i:s');

            return $o;
        }, $orders);
        Log::info('Finish formatting orders');

        $chunks = array_chunk($ordersData, 250);

        foreach ($chunks as $chunk) {
            CachedOrder::insert($chunk);
        }
    }

    private function _saveSettings() {
        Setting::setData('market_orders_update', json_encode($this->_settings));
    }

    private function _saveMarketHistoryUpdateData() {
        Setting::setData('market_history_update_data', json_encode($this->_marketHistoryUpdateData));
    }

    private function _retry($callback, int $times = 1) {
        for ($i = 1; $i <= $times; $i++) {
            try {
                Log::info('Start making request');
                return $callback();
            } catch (\Throwable $t) {
                Log::error('Request failed');
                Log::error($t->getMessage());
                Log::error($t->getTraceAsString());
                sleep(1);
                continue;
            }
        }

        Log::error('Exhausted retries count');
        throw new \Exception('Exhausted retries count');
    }

    private function _logMemoryUsage() {
        $size = memory_get_usage();
        $peakSize = memory_get_peak_usage();

        $unit=array('b','kb','mb','gb','tb','pb');

        Log::info(@round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]);
        Log::info(@round($peakSize/pow(1024,($i=floor(log($peakSize,1024)))),2).' '.$unit[$i]);
    }
}