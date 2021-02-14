<?php

namespace App\Console\Commands;

use App\Services\DataAggregation;
use Illuminate\Console\Command;

class AggregatePrices extends Command {

    /**
     * @var string
     */
    protected $signature = 'aggregate:prices';

    /**
     * @var string
     */
    protected $description = 'Aggregate prices';

    public function handle(DataAggregation\Controller $controller): int {
        $controller->aggregatePrices();

        return 0;
    }
}
