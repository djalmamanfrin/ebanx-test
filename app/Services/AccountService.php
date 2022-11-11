<?php

namespace App\Services;

use App\Models\Account;

class AccountService
{
    protected Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function getBalance(): int
    {
        $transactions = $this->account->transactions()->get();
        if ($transactions->isEmpty()) {
            return 0;
        }
        return $transactions->pluck('amount')->sum();
    }
}
