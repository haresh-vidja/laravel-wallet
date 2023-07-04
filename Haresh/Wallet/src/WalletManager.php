<?php
namespace Haresh\Wallet;

use Haresh\Wallet\Models\Wallet;
use Haresh\Wallet\Models\Transaction;
use Illuminate\Support\Str;

/**
 * Class WalletManager
 * 
 * Handles wallet operations: credit, debit, transfer, rollback, and event handling.
 */
class WalletManager
{
    /**
     * Credit a user's wallet.
     */
    public function credit($user, $amount, $description = null, array $meta = [], $status = 'approved')
    {
        $wallet = $user->wallet ?? Wallet::create(['user_id' => $user->id, 'balance' => 0]);

        if ($status === 'approved') {
            $wallet->balance += $amount;
            $wallet->save();
        }

        $wallet->transactions()->create([
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description,
            'meta' => json_encode($meta),
            'reference' => (string) Str::uuid(),
            'status' => $status
        ]);

        return $wallet;
    }

    /**
     * Debit a user's wallet.
     */
    public function debit($user, $amount, $description = null, array $meta = [], $status = 'approved')
    {
        $wallet = $user->wallet;

        if ($status === 'approved') {
            if (!$wallet || $wallet->balance < $amount) {
                throw new \Exception("Insufficient balance");
            }

            $wallet->balance -= $amount;
            $wallet->save();
        }

        $wallet->transactions()->create([
            'amount' => $amount,
            'type' => 'debit',
            'description' => $description,
            'meta' => json_encode($meta),
            'reference' => (string) Str::uuid(),
            'status' => $status
        ]);

        return $wallet;
    }

    /**
     * Transfer money from one user to another.
     */
    public function transfer($fromUser, $toUser, $amount, $description = 'Wallet Transfer')
    {
        $this->debit($fromUser, $amount, "Transfer to User #{$toUser->id}");
        $this->credit($toUser, $amount, "Transfer from User #{$fromUser->id}");

        return true;
    }

    /**
     * Rollback a transaction by creating its opposite.
     */
    public function rollback(Transaction $transaction)
    {
        $oppositeType = $transaction->type === 'credit' ? 'debit' : 'credit';
        $user = $transaction->wallet->user;

        return $this->{$oppositeType}($user, $transaction->amount, "Rollback of Txn #{$transaction->id}");
    }
}
