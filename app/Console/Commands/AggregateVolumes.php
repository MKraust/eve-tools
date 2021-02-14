<?php

namespace App\Console\Commands;

use App\Services\DataAggregation;
use Illuminate\Console\Command;

class AggregateVolumes extends Command {

    /**
     * @var string
     */
    protected $signature = 'aggregate:volumes';

    /**
     * @var string
     */
    protected $description = 'Aggregate volumes';

    public function handle(DataAggregation\Controller $controller): int {
        $controller->aggregateVolumes();

        return 0;
    }
}
