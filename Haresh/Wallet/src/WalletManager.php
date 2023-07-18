<?php
namespace Haresh\Wallet;

use Haresh\Wallet\Models\Wallet;
use Haresh\Wallet\Models\Transaction;
use Illuminate\Support\Str;

/**
 * WalletManager
 * 
 * Central handler for transactions on any wallet.
 */
class WalletManager
{
    public function credit(Wallet $wallet, $amount, $description = null, array $meta = [], $status = 'approved')
    {
        if ($status === 'approved') {
            $wallet->balance += $amount;
            $wallet->save();
        }

        return $wallet->transactions()->create([
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description,
            'meta' => json_encode($meta),
            'reference' => (string) Str::uuid(),
            'status' => $status
        ]);
    }

    public function debit(Wallet $wallet, $amount, $description = null, array $meta = [], $status = 'approved')
    {
        if ($status === 'approved') {
            if ($wallet->balance < $amount) {
                throw new \Exception("Insufficient balance");
            }

            $wallet->balance -= $amount;
            $wallet->save();
        }

        return $wallet->transactions()->create([
            'amount' => $amount,
            'type' => 'debit',
            'description' => $description,
            'meta' => json_encode($meta),
            'reference' => (string) Str::uuid(),
            'status' => $status
        ]);
    }

    public function transfer(Wallet $fromWallet, Wallet $toWallet, $amount, $description = 'Wallet Transfer')
    {
        $this->debit($fromWallet, $amount, "Transfer to Wallet #{$toWallet->id}");
        $this->credit($toWallet, $amount, "Transfer from Wallet #{$fromWallet->id}");
        return true;
    }

    public function rollback(Transaction $transaction)
    {
        $oppositeType = $transaction->type === 'credit' ? 'debit' : 'credit';
        $wallet = $transaction->wallet;

        return $this->{$oppositeType}(
            $wallet,
            $transaction->amount,
            "Rollback of Txn #{$transaction->id}",
            [],
            'approved'
        );
    }
}
