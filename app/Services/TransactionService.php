<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Event;
use App\TypesEnum;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

abstract class TransactionService
{
    const MINIMUM_ALLOWED_VALUE = 1;

    protected Account $account;
    protected Event $event;
    protected float $amount;

    public function __construct(Account $account, Event $event)
    {
        $this->account = $account;
        $this->event = $event;
        $this->amount = 0;
    }

    protected function isTheMinimumAllowed(float $amount): bool
    {
        return $amount >= self::MINIMUM_ALLOWED_VALUE;
    }
}
