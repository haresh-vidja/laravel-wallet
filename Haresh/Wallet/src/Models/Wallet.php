<?php
namespace Haresh\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Haresh\Wallet\Models\Transaction;

/**
 * Class Wallet
 * 
 * Represents a standalone wallet not tied to a user by foreign key.
 */
class Wallet extends Model
{
    protected $fillable = ['type', 'balance'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
