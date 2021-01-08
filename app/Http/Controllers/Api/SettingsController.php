<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function getMarketOrdersUpdateInfo() {
        return setting('market_orders_update') ?: 'null';
    }

    public function refreshMarketOrders() {
        \App\Jobs\RefreshMarketOrders::dispatchAfterResponse();

        return ['status' => 'success'];
    }
}
