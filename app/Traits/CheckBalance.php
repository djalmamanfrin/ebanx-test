<?php

namespace App\Traits;

trait CheckBalance
{
    public function hasFound(float $amount): bool
    {
        $this->checkingMinimumAllowed($amount);
        $balance = $this->account->getBalance();
        return $balance >= $amount;
    }
}
