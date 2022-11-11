<?php

namespace App\Services;

use App\Models\Account;

class DepositService
{
    const MINIMUM_ALLOWED_VALUE = 1;

    private Account $account;
    private float $amount;

    public function __construct(Account $account, float $amount)
    {
        $this->account = $account;
        $this->setAmount($amount);
    }

    private function isTheMinimumAllowed(float $amount): bool
    {
        return $amount >= self::MINIMUM_ALLOWED_VALUE;
    }

    private function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
}
