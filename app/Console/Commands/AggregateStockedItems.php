<?php

namespace App\Console\Commands;

use App\Services\DataAggregation;
use Illuminate\Console\Command;

class AggregateStockedItems extends Command {

    /**
     * @var string
     */
    protected $signature = 'aggregate:stock';

    /**
     * @var string
     */
    protected $description = 'Aggregate stocked items';

    public function handle(DataAggregation\Controller $controller): int {
        $controller->aggregateStockedItems();

        return 0;
    }
}
