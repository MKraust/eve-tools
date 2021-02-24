<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataRefreshment;

class RefreshContracts extends Command
{

    protected $signature = 'refresh:contracts';

    protected $description = 'Refresh contracts';

    public function handle(DataRefreshment\Controller $refreshmentController): int {
        $refreshmentController->refreshContracts();

        return 0;
    }
}
