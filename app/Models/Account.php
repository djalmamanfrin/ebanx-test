<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getBalance(): int
    {
        $transactions = $this->transactions()->get();
        if ($transactions->isEmpty()) {
            return 0;
        }
        return $transactions->pluck('amount')->sum();
    }
}
