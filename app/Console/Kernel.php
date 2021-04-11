<?php

namespace App\Console;

use App\Console\Commands;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\RefreshPrices::class,
        Commands\RefreshOrders::class,
        Commands\RefreshMarketHistory::class,
        Commands\RefreshTransactions::class,
        Commands\RefreshIndustryIndices::class,
        Commands\RefreshAssets::class,

        Commands\AggregateVolumes::class,
        Commands\AggregatePrices::class,
        Commands\AggregateCharacterOrders::class,
        Commands\AggregateStockedItems::class,
        Commands\AggregateProfits::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(Commands\RefreshOrders::class)->everyTenMinutes();
        $schedule->command(Commands\RefreshPrices::class)->everyTenMinutes();
        $schedule->command(Commands\RefreshIndustryIndices::class)->everyTenMinutes();
        $schedule->command(Commands\RefreshAssets::class)->everyTenMinutes();
        $schedule->command(Commands\RefreshTransactions::class)->hourly();
        $schedule->command(Commands\AggregateProfits::class)->hourly();
        $schedule->command(Commands\RefreshMarketHistory::class)->dailyAt('14:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
