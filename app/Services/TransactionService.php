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
    protected Account $account;
    protected Event $event;
    protected float $amount;

    public function __construct(Account $account, Event $event)
    {
        $this->account = $account;
        $this->event = $event;
        $this->amount = 0;
    }
}
