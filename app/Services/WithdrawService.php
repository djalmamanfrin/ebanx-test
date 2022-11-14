<?php

namespace App\Services;

use App\Traits\CheckBalance;

class WithdrawService extends TransactionService
{
    use CheckBalance;

    public function persist(): bool
    {
        $this->checkingMinimumAllowed();
        $this->checkingHasFund();
        $attributes = [
            'account_id' => $this->account->id,
            'event_id' => $this->event->id,
            'amount' => $this->amount * -1
        ];
        return $this->account->transactions()
            ->make($attributes)
            ->saveOrFail();
    }
}
