<?php
namespace Haresh\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transaction
 * 
 * Refers to a specific wallet via wallet_id. Carries unique reference.
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
