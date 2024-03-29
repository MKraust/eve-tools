<?php

namespace App\Services\DataRefreshment;

use App\Models\CachedTransaction;
use App\Models\Character;
use App\Services;

class TransactionsRefresher {

    private Character $_character;

    public function __construct(Character $character) {
        $this->_character = $character;

        $this->_esi = new Services\ESI($character);
    }

    public function refresh(): void {
        $transactions = $this->_esi->getWalletTransactions($this->_character->id);

        $transactionsData = [];
        foreach ($transactions as $transaction) {
            $transactionsData[] = [
                'character_id' => $this->_character->id,
                'client_id' => $transaction->client_id,
                'date' => (new \DateTime($transaction->date))->format('Y-m-d H:i:s'),
                'is_buy' => $transaction->is_buy,
                'is_personal' => $transaction->is_personal,
                'journal_ref_id' => $transaction->journal_ref_id,
                'location_id' => $transaction->location_id,
                'quantity' => $transaction->quantity,
                'transaction_id' => $transaction->transaction_id,
                'type_id' => $transaction->type_id,
                'unit_price' => $transaction->unit_price,
            ];
        }

        $chunks = array_chunk(array_values($transactionsData), 1000);
        foreach ($chunks as $chunk) {
            CachedTransaction::insertOrIgnore($chunk);
        }
    }
}
