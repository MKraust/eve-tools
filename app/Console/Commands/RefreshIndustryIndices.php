<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataRefreshment;

class RefreshIndustryIndices extends Command
{

    protected $signature = 'refresh:indices';

    protected $description = 'Refresh industry indices';

    public function handle(DataRefreshment\Controller $refreshmentController): int {
        $refreshmentController->refreshIndustryIndices();

        return 0;
    }
}
