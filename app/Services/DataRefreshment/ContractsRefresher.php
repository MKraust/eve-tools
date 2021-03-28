<?php

namespace App\Services\DataRefreshment;

use App\Models;
use App\Services;

class ContractsRefresher {

    private Services\ESI $_esi;

    private Models\Character $_character;

    /** @var int[] */
    private array $_newContractIds = [];

    /** @var int[] */
    private array $_existedContractIds = [];

    public function __construct(Models\Character $character) {
        $this->_character = $character;
        $this->_esi = new Services\ESI($character);
    }

    public function refresh(): void {
        $this->_existedContractIds = Models\CachedContract::select('contract_id')->get()->map->contract_id->toArray();

        $this->_refreshContracts();
        $this->_refreshContractItems();
    }

    private function _refreshContracts(): void {
        $page = 1;

        do {
            $contracts = retry(4, function () use ($page) {
                return $this->_esi->getCharacterContracts($this->_character->id, $page);
            }, 1000);

            $collection = collect($contracts->getArrayCopy());
            $filteredContracts = $collection->filter(function ($contract) {
                return !in_array($contract->contract_id, $this->_existedContractIds, true);
            });

            foreach ($filteredContracts as $filteredContract) {
                $this->_newContractIds[] = $filteredContract->contract_id;
            }

            $this->_storeContracts($filteredContracts->toArray());

            $page++;
        } while ($page <= $contracts->pages);
    }

    private function _storeContracts(array $contracts): void {
        foreach ($contracts as $contract) {
            $c = json_decode(json_encode($contract), true);
            $c['character_id'] = $this->_character->id;

            $c['date_accepted'] = ($c['date_accepted'] ?? null) ? (new \DateTime($c['date_accepted']))->format('Y-m-d H:i:s') : null;
            $c['date_completed'] = ($c['date_completed'] ?? null) ? (new \DateTime($c['date_completed']))->format('Y-m-d H:i:s') : null;
            $c['date_expired'] = ($c['date_expired'] ?? null) ? (new \DateTime($c['date_expired']))->format('Y-m-d H:i:s') : null;
            $c['date_issued'] = ($c['date_issued'] ?? null) ? (new \DateTime($c['date_issued']))->format('Y-m-d H:i:s') : null;

            $model = new Models\CachedContract($c);
            $model->save();
        }
    }

    private function _refreshContractItems(): void {
        foreach ($this->_newContractIds as $contractId) {
            $items = retry(4, function () use ($contractId) {
                return $this->_esi->getContractItems($this->_character->id, $contractId);
            }, 1000);

            $this->_storeContractItems($contractId, $items->getArrayCopy());
        }
    }

    private function _storeContractItems(int $contractId, array $items): void {
        foreach ($items as $item) {
            $c = json_decode(json_encode($item), true);
            $c['contract_id'] = $contractId;

            $model = new Models\CachedContractItem($c);
            $model->save();
        }
    }
}
