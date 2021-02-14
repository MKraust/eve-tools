<?php

namespace App\Console\Commands;

use App\Services\DataRefreshment;
use Illuminate\Console\Command;

class RefreshOrders extends Command {

    /**
     * @var string
     */
    protected $signature = 'refresh:orders';

    /**
     * @var string
     */
    protected $description = 'Refresh market orders';

    public function handle(DataRefreshment\Controller $refreshmentController): int {
        $refreshmentController->refreshMarketOrders();

        return 0;
    }
}
