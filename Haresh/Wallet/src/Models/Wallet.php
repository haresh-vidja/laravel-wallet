<?php
namespace Haresh\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Haresh\Wallet\Models\Transaction;

/**
 * Class Wallet
 * 
 * Represents the wallet associated with a user.
 */
class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];

    /**
     * Relationship: A wallet has many transactions.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
