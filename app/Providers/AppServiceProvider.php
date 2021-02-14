<?php

namespace App\Providers;

use App\Services\DataRefreshment;
use App\Services\DataAggregation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DataRefreshment\Controller::class);
        $this->app->singleton(DataAggregation\Controller::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
