<?php

namespace App\Traits;

use InvalidArgumentException;

trait CheckBalance
{
    public function checkingHasFund(): void
    {
        $this->checkingMinimumAllowed();
        $balance = $this->account->getBalance();
        $transactionAmount = $this->amount;
        if ($balance < $transactionAmount) {
            $message = "The amount %s informed is greater than balance %s in account";
            throw new InvalidArgumentException(sprintf($message, $transactionAmount, $balance));
        }
    }
}
