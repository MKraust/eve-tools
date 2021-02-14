<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\RefreshOrders;
use App\Jobs\RefreshMarketHistory;
use App\Models\Setting;
use App\Services\Locations\Keeper;
use App\Services\Locations\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    private $_locationKeeper;

    public function __construct(Keeper $locationKeeper) {
        $this->_locationKeeper = $locationKeeper;
    }

    public function getMarketOrdersUpdateInfo() {
        $info = Setting::getData('market_orders_update');

        return $info->value ?? 'null';
    }

    public function getMarketHistoryUpdateInfo() {
        $info = Setting::getData('market_history_update_data');

        return $info->value ?? 'null';
    }

    public function refreshMarketData() {
        $this->_runJobAsync(RefreshOrders::class);

        return ['status' => 'success'];
    }

    public function refreshMarketHistory() {
        $this->_runJobAsync(RefreshMarketHistory::class);

        return ['status' => 'success'];
    }

    private function _runJobAsync(string $jobClass) {
        register_shutdown_function(function () use ($jobClass) {
            $phpSapiName = php_sapi_name();
            if (in_array($phpSapiName, ['fpm-fcgi', 'cli-server'])) {
                fastcgi_finish_request();
            }

            $jobClass::dispatchSync();
        });
    }
}
