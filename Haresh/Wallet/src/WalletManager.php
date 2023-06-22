<?php
namespace Haresh\Wallet;

use Haresh\Wallet\Models\Wallet;
use Haresh\Wallet\Models\Transaction;

/**
 * Class WalletManager
 * 
 * Handles core wallet operations like credit and debit.
 */
class WalletManager
{
    /**
     * Credit a user's wallet.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param float $amount
     * @param string|null $description
     * @return Wallet
     */
    public function credit($user, $amount, $description = null)
    {
        $wallet = $user->wallet ?? Wallet::create(['user_id' => $user->id, 'balance' => 0]);
        $wallet->balance += $amount;
        $wallet->save();

        $wallet->transactions()->create([
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description
        ]);

        return $wallet;
    }

    /**
     * Debit a user's wallet.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param float $amount
     * @param string|null $description
     * @throws \Exception
     * @return Wallet
     */
    public function debit($user, $amount, $description = null)
    {
        $wallet = $user->wallet;
        if (!$wallet || $wallet->balance < $amount) {
            throw new \Exception("Insufficient balance");
        }

        $wallet->balance -= $amount;
        $wallet->save();

        $wallet->transactions()->create([
            'amount' => $amount,
            'type' => 'debit',
            'description' => $description
        ]);

        return $wallet;
    }
}
