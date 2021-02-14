<?php

namespace App\Console;

use App\Console\Commands;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\RefreshOrders::class,
        Commands\RefreshMarketHistory::class,
        Commands\RefreshTransactions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new Jobs\RefreshOrders)->everyFifteenMinutes();
        $schedule->job(new Jobs\RefreshPrices)->everyFifteenMinutes();
        $schedule->job(new Jobs\RefreshMarketHistory)->dailyAt('14:30');
        $schedule->job(new Jobs\RefreshTransactions)->hourly();
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
