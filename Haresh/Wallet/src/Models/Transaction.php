<?php
namespace Haresh\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 * 
 * Represents a single credit or debit in the wallet.
 */
class Transaction extends Model
{
    protected $fillable = ['wallet_id', 'amount', 'type', 'description'];
}
