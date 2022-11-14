<?php

namespace App\Services;

class DepositService extends TransactionService
{
    public function persist(): bool
    {
        $this->checkingMinimumAllowed();
        $attributes = [
            'account_id' => $this->account->id,
            'event_id' => $this->event->id,
            'amount' => $this->amount
        ];
        return $this->account->transactions()
            ->make($attributes)
            ->saveOrFail();
    }
}
