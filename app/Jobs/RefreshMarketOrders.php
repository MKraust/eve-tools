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
        $this->_clearCachedOrders();

        $this->_refreshDichstarOrders();
        $this->_refreshJitaOrders();
    }

    private function _clearCachedOrders() {
        DB::table('cached_orders')->truncate();
    }

    private function _refreshJitaOrders() {
        $page = 1;
        do {
            $orders = $this->_esi->getMarketOrders(10000002, 'sell', $page);
            $jitaOrdersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return $order->location_id === 60003760;
            });

            $this->_storeOrders($jitaOrdersCollection->toArray());

            $page++;
        } while ($page <= $orders->pages);
    }

    private function _refreshDichstarOrders() {
        $page = 1;
        do {
            $orders = $this->_esi->getStructureOrders(1031787606461, $page);
            $jitaOrdersCollection = collect($orders->getArrayCopy())->filter(function ($order) {
                return !$order->is_buy_order;
            });

            $this->_storeOrders($jitaOrdersCollection->toArray());

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
