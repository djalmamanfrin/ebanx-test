<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'account_id', 'type', 'origin', 'destination', 'amount'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

}
