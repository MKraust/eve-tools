<?php

namespace App\Jobs;

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

    private function _clearCachedOrders() {
        DB::table('cached_orders')->truncate();

        $this->_settings['progress']['is_table_cleared'] = true;
        $this->_saveSettings();
    }

    private function _refreshJitaOrders() {
        $page = 1;
        do {
            $orders = $this->_retry(function () use ($page) {
               return $this->_esi->getMarketOrders(10000002, 'sell', $page);
            }, 4);
            Log::info('Request succeeded');

            $ordersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return $order->location_id === 60003760;
            });

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
            $orders = $this->_retry(function () use ($page) {
                return $this->_esi->getStructureOrders(1031787606461, $page);
            }, 4);
            Log::info('Request succeeded');

            $ordersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return !$order->is_buy_order;
            });

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
        $ordersData = array_map(function ($order) {
            $o = json_decode(json_encode($order), true);
            $o['issued'] = (new \DateTime($o['issued']))->format('Y-m-d H:i:s');

            return $o;
        }, $orders);

        CachedOrder::insert($ordersData);
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
}
