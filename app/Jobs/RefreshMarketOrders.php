<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services;
use App\Models\CachedOrder;
use Illuminate\Support\Facades\DB;

class RefreshMarketOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $_esi;

    public function __construct()
    {
        $this->_esi = new Services\ESI;
    }

    public function handle()
    {
        $status = setting('market_orders_update.status');
        if ($status === 'in_progress') {
            return;
        }

        setting()->set('market_orders_update', [
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
        ])->save();

        try {
            $this->_clearCachedOrders();

            $this->_refreshDichstarOrders();
            $this->_refreshJitaOrders();
        } catch (\Throwable $t) {
            setting()->set('market_orders_update.status', 'error')->save();
            throw $t;
        }

        setting()->set('market_orders_update.status', 'finished')
                 ->set('market_orders_update.end_date', (new \DateTime)->format('Y-m-d H:i:s'))
                 ->save();
    }

    private function _clearCachedOrders() {
        DB::table('cached_orders')->truncate();
        setting()->set('market_orders_update.progress.is_table_cleared', true)->save();
    }

    private function _refreshJitaOrders() {
        $page = 1;
        do {
            $orders = $this->_esi->getMarketOrders(10000002, 'sell', $page);
            setting()->set('market_orders_update.progress.jita.total_pages', $orders->pages)->save();
            $jitaOrdersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return $order->location_id === 60003760;
            });

            $this->_storeOrders($jitaOrdersCollection->toArray());

            setting()->set('market_orders_update.progress.jita.processed_pages', $page)->save();
            $page++;
        } while ($page <= $orders->pages);
    }

    private function _refreshDichstarOrders() {
        $page = 1;
        do {
            $orders = $this->_esi->getStructureOrders(1031787606461, $page);
            setting()->set('market_orders_update.progress.dichstar.total_pages', $orders->pages)->save();
            $jitaOrdersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return !$order->is_buy_order;
            });

            $this->_storeOrders($jitaOrdersCollection->toArray());


            setting()->set('market_orders_update.progress.dichstar.processed_pages', $page)->save();
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
}
