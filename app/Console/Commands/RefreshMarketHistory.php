<?php

namespace App\Console\Commands;

use App\Services\DataRefreshment;
use Illuminate\Console\Command;

class RefreshMarketHistory extends Command {

    /**
     * @var string
     */
    protected $signature = 'refresh:history';

    /**
     * @var string
     */
    protected $description = 'Refresh market history';

    public function handle(DataRefreshment\Controller $refreshmentController): int {
        $refreshmentController->refreshMarketHistory();

        return 0;
    }
}
