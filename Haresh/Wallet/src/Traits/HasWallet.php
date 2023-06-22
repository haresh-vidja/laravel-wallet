<?php
namespace Haresh\Wallet\Traits;

use Haresh\Wallet\Models\Wallet;

/**
 * Trait HasWallet
 * 
 * Adds wallet relationship and wallet operations to a user model.
 */
trait HasWallet
{
    /**
     * Define one-to-one relationship with Wallet model.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Credit the user's wallet.
     */
    public function credit($amount, $description = null)
    {
        return app('wallet')->credit($this, $amount, $description);
    }

    /**
     * Debit the user's wallet.
     */
    public function debit($amount, $description = null)
    {
        return app('wallet')->debit($this, $amount, $description);
    }
}
