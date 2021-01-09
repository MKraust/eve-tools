<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function getMarketOrdersUpdateInfo() {
        $info = Setting::getData('market_orders_update');

        return $info !== null ? json_encode($info->value) : 'null';
    }

    public function refreshMarketOrders() {
        register_shutdown_function(function () {
            try {
                if (php_sapi_name() === 'fpm-fcgi') {
                    fastcgi_finish_request();
                }

                \App\Jobs\RefreshMarketOrders::dispatchSync();
            } catch (\Throwable $t) {
                Log::error($t->getMessage());
                Log::error($t->getTraceAsString());
                throw $t;
            }
        });

        return ['status' => 'success'];
    }
}
