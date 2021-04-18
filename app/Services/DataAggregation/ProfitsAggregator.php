<?php

namespace App\Services\DataAggregation;

use App\Models\AggregatedProfit;
use App\Models\CachedTransaction;
use App\Models\Character;
use App\Models\SDE\Inventory\Type;
use App\Services\Locations;
use Illuminate\Support\Collection;

class ProfitsAggregator {

    private const CHUNK_SIZE = 100;

    private Locations\Keeper $_locationsKeeper;

    public function __construct() {
        $this->_locationsKeeper = app(Locations\Keeper::class);
    }

    public function aggregate(): void {
        $unprocessedSellTransactionsCount = CachedTransaction::sell()->unprocessed()->count();
        logger("Unprocessed sell transactions: {$unprocessedSellTransactionsCount}");

        for ($offset = 0; $offset < $unprocessedSellTransactionsCount; $offset += self::CHUNK_SIZE) {
            logger("Processed transactions: {$offset}");
            $sellTransactions = CachedTransaction::sell()->unprocessed()->earliest()->limit(self::CHUNK_SIZE)->offset($offset)->get();
            $this->_processSellTransactions($sellTransactions);
        }
    }

    private function _processSellTransactions(Collection $sellTransactions): void {
        foreach ($sellTransactions as $sell) {
            $sellCharacter = Character::find($sell->character_id);
            if ($sellCharacter === null) {
                throw new \Exception("Character {$sell->character_id} not found. Transaction {$sell->transaction_id}");
            }

            $type = Type::find($sell->type_id);

            logger("Processing sell transaction {$sell->transaction_id}");
            while ($sell->processed_quantity < $sell->quantity) {
                $buy = CachedTransaction::buy()->unprocessed()->where('type_id', $sell->type_id)->where('date', '<', $sell->date)->earliest()->first();

                if ($buy === null) {
                    logger('No buy transactions left, finish processing');
                    $sell->processed_quantity = $sell->quantity;
                    break;
                }

                $quantityToProcess = min($buy->quantityToProcess, $sell->quantityToProcess);
                logger("Quantity to process: {$quantityToProcess}");

                $sell->processed_quantity += $quantityToProcess;
                $buy->processed_quantity += $quantityToProcess;

                $profitRecord = $this->_createProfitRecord($type, $sellCharacter, $buy, $sell, $quantityToProcess);

                $profitRecord->save();
                $buy->save();

                logger("Saved profit record {$profitRecord->id}. Quantity to process left: {$sell->quantityToProcess}");
            }

            $sell->save();
            logger("Finished processing sell transaction {$sell->transaction_id}");
            logMemory();
        }
    }

    private function _createProfitRecord(Type $type, Character $sellCharacter, CachedTransaction $buy, CachedTransaction $sell, int $quantityToProcess): AggregatedProfit {
        $margin = ($sell->unit_price - $buy->unit_price) * $quantityToProcess;

        $buyLocation = $this->_locationsKeeper->getById($buy->location_id);
        $sellLocation = $this->_locationsKeeper->getById($sell->location_id);
        $deliveryCost = $buyLocation && $sellLocation && $buyLocation->id() !== $sellLocation->id() ? $type->getDeliveryCost($sellLocation) * $quantityToProcess : 0;

        $buyCharacter = Character::find($buy->character_id);
        $buyCharacterBrokerFeePercent = $buyCharacter !== null ? $buyCharacter->buy_broker_fee_percent : 0;
        $buyBrokerFee = round($buy->unit_price * ($buyCharacterBrokerFeePercent / 100) * $quantityToProcess, 2);

        $sellCharacterBrokerFeePercent = $sellCharacter->sell_broker_fee_percent ?? 0;
        $sellBrokerFee = round($sell->unit_price * ($sellCharacterBrokerFeePercent / 100) * $quantityToProcess, 2);

        $sellCharacterSalesTaxPercent = $sellCharacter->sales_tax_percent ?? 0;
        $salesTax = round($sell->unit_price * ($sellCharacterSalesTaxPercent / 100) * $quantityToProcess, 2);

        $profit = ($sell->unit_price - $buy->unit_price) * $quantityToProcess - $deliveryCost - $buyBrokerFee - $sellBrokerFee - $salesTax;

        $profitRecord = new AggregatedProfit;
        $profitRecord->sell_transaction_id = $sell->transaction_id;
        $profitRecord->buy_transaction_id = $buy->transaction_id;
        $profitRecord->quantity = $quantityToProcess;
        $profitRecord->type_id = $sell->type_id;
        $profitRecord->date = $sell->date;
        $profitRecord->margin = $margin;
        $profitRecord->delivery_cost = $deliveryCost;
        $profitRecord->buy_broker_fee = $buyBrokerFee;
        $profitRecord->sell_broker_fee = $sellBrokerFee;
        $profitRecord->sales_tax = $salesTax;
        $profitRecord->profit = $profit;

        return $profitRecord;
    }
}
