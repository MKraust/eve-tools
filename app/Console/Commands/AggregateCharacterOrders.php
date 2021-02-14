<?php

namespace App\Console\Commands;

use App\Services\DataAggregation;
use Illuminate\Console\Command;

class AggregateCharacterOrders extends Command {

    /**
     * @var string
     */
    protected $signature = 'aggregate:orders';

    /**
     * @var string
     */
    protected $description = 'Aggregate characters orders';

    public function handle(DataAggregation\Controller $controller): int {
        $controller->aggregateCharactersOrders();

        return 0;
    }
}
