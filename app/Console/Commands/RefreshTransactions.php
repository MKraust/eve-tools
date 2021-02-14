<?php

namespace App\Console\Commands;

use App\Services\DataRefreshment;
use Illuminate\Console\Command;

class RefreshTransactions extends Command {

    /**
     * @var string
     */
    protected $signature = 'refresh:transactions';

    /**
     * @var string
     */
    protected $description = 'Refresh characters transactions';

    public function handle(DataRefreshment\Controller $refreshmentController): int {
        $refreshmentController->refreshTransactions();

        return 0;
    }
}
