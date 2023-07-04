<?php
namespace Haresh\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transaction
 * 
 * Represents a wallet transaction with rollback, reference ID, and metadata support.
 */
class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wallet_id', 'amount', 'type', 'description', 'meta', 'reference', 'status'
    ];

    protected $casts = [
        'meta' => 'array'
    ];
}
