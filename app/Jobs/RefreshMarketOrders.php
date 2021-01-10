<?php

namespace App\Jobs;

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

class RefreshMarketOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $_esi;

    private $_settings;

    private $_minJitaPrices = [];

    private $_minDichstarPrices = [];

    public function __construct()
    {
        $this->_esi = new Services\ESI;

        $settings = Setting::getData('market_orders_update');
        $this->_settings = $settings !== null ? json_decode($settings->value, true) : null;
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
                'is_table_cleared' => false,
                'jita'             => [
                    'total_pages'     => null,
                    'processed_pages' => 0,
                ],
                'dichstar'         => [
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

            $this->_refreshPrices();
            $this->_refreshSystemIndustryIndices();
        } catch (\Throwable $t) {
            $this->_settings['status'] = 'error';
            $this->_saveSettings();

            Log::info($t->getMessage());

            throw $t;
        }

        $this->_settings['status'] = 'finished';
        $this->_settings['end_date'] = (new \DateTime)->format('Y-m-d H:i:s');
        $this->_saveSettings();
    }

    private function _refreshSystemIndustryIndices() {
        $industrySystems = $this->_esi->getIndustrySystems();
        $dichstarSystem = collect($industrySystems)->first(function ($system) {
            return $system->solar_system_id === 30000843;
        });

        Setting::setData('industry_indices', json_encode($dichstarSystem->cost_indices));
    }

    private function _refreshPrices() {
        $pricesData = [];
        foreach ($this->_minJitaPrices as $typeId => $price) {
            $pricesData[$typeId] = ['type_id' => $typeId, 'jita' => $price, 'dichstar' => null, 'average' => null, 'adjusted' => null];
        }

        foreach ($this->_minDichstarPrices as $typeId => $price) {
            $priceData = $pricesData[$typeId] ?? ['type_id' => $typeId, 'jita' => null, 'dichstar' => null, 'average' => null, 'adjusted' => null];
            $priceData['dichstar'] = $price;
            $pricesData[$typeId] = $priceData;
        }

        $apiPrices = $this->_esi->getMarketPrices();
        foreach ($apiPrices as $price) {
            $priceData = $pricesData[$price->type_id] ?? ['type_id' => $price->type_id, 'jita' => null, 'dichstar' => null, 'average' => null, 'adjusted' => null];
            $priceData['average'] = $price->average_price ?? null;
            $priceData['adjusted'] = $price->adjusted_price ?? null;
            $pricesData[$price->type_id] = $priceData;
        }

        DB::table('cached_prices')->truncate();
        CachedPrice::insert(array_values($pricesData));
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
               return $this->_esi->getMarketOrders(10000002, 'sell', $page);
            }, 4);
            Log::info('Request succeeded');

            $ordersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return $order->location_id === 60003760;
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
                return $this->_esi->getStructureOrders(1031787606461, $page);
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
