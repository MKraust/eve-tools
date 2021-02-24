<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataRefreshment;

class RefreshAssets extends Command
{

    protected $signature = 'refresh:assets';

    protected $description = 'Refresh assets';

    public function handle(DataRefreshment\Controller $refreshmentController): int {
        $refreshmentController->refreshAssets();

        return 0;
    }
}
