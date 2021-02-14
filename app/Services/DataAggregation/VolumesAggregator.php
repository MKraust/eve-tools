<?php

namespace App\Services\DataAggregation;

use App\Models\AggregatedVolume;
use App\Models\CachedOrdersHistory;
use App\Models\CachedPrice;

class VolumesAggregator {

    /** @var int */
    private $_regionId;

    public function __construct(int $regionId) {
        $this->_regionId = $regionId;
    }

    public function aggregate(): void {
        $typeIds = CachedPrice::all()->map->type_id->toArray();
        $typeIdsChunks = array_chunk($typeIds, 100);

        $volumesData = [];
        foreach ($typeIdsChunks as $typeIdsChunk) {
            $monthAgo = now()->modify('-30 days')->format('Y-m-d');
            $ordersHistoryData = CachedOrdersHistory::where('region_id', $this->_regionId)
                                                    ->whereIn('type_id', $typeIdsChunk)
                                                    ->where('date', '>=', $monthAgo)
                                                    ->get();

            foreach ($ordersHistoryData->groupBy('type_id') as $typeId => $typeOrdersHistoryData) {
                $volumeData = [
                    'region_id' => $this->_regionId,
                    'type_id' => $typeId,
                    'monthly' => null,
                    'weekly' => null,
                    'average_daily' => null,
                ];

                $monthlyVolume = $typeOrdersHistoryData->sum('volume');

                $volumeData['average_daily'] = round($monthlyVolume / 30, 2);
                $volumeData['monthly'] = $monthlyVolume;
                $volumeData['weekly'] = $typeOrdersHistoryData->filter(function ($historyDatum) {
                    return new \DateTime($historyDatum->date) >= now()->modify('-7 days');
                })->sum->volume;

                $volumesData[] = $volumeData;
            }
        }

        AggregatedVolume::where('region_id', $this->_regionId)->delete();
        AggregatedVolume::insert($volumesData);
    }
}
