<?php

namespace App\Jobs;

use App\Models\CachedTransaction;
use App\Services\ESI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const JIN_KRAUST_CHARACTER_ID = 2117638152;

    private $_esi;

    public function __construct() {
        $this->_esi = new ESI();
    }

    public function handle() {
        $transactions = $this->_esi->getWalletTransactions(self::JIN_KRAUST_CHARACTER_ID);

        $transactionsData = [];
        foreach ($transactions as $transaction) {
            $transactionsData[] = [
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
