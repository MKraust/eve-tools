<?php

namespace App\Providers;

use App\Events\MarketHistoryRefreshed;
use App\Events\OrdersRefreshed;
use App\Events\StockRefreshed;
use App\Services\DataAggregation\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(function (MarketHistoryRefreshed $event) {
            app(Controller::class)->aggregateVolumes();
        });

        Event::listen(function (OrdersRefreshed $event) {
            app(Controller::class)->aggregatePrices();
            app(Controller::class)->aggregateCharactersOrders();
        });

        Event::listen(function (StockRefreshed $event) {
            app(Controller::class)->aggregateStockedItems();
        });
    }
}
