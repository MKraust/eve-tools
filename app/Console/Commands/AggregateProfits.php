<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataAggregation;

class AggregateProfits extends Command
{
    protected $signature = 'aggregate:profits';

    protected $description = 'Aggregate trading profits';

    public function handle(DataAggregation\Controller $controller): int {
        $controller->aggregateProfits();

        return 0;
    }
}
