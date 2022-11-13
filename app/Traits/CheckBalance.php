<?php

namespace App\Traits;

trait CheckBalance
{
    public function hasFound(float $amount): bool
    {
        $balance = $this->account->getBalance();
        return $balance >= $amount;
    }
}
