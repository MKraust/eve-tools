<?php

namespace App\Console\Commands;

use App\Services\DataRefreshment;
use Illuminate\Console\Command;

class RefreshPrices extends Command {

    /**
     * @var string
     */
    protected $signature = 'refresh:prices';

    /**
     * @var string
     */
    protected $description = 'Refresh prices';

    public function handle(DataRefreshment\Controller $refreshmentController): int {
        $refreshmentController->refreshPrices();

        return 0;
    }
}
