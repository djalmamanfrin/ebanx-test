<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Account;

class TransactionManager
{
    private Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    private function getAccount(int $accountId): Account
    {
        $service = new AccountService($accountId);
        return $service->get();
    }

    public function getOriginAccount(): Account
    {
        $originAccountId = $this->event->origin;
        return $this->getAccount($originAccountId);
    }

    public function getDestinationAccount(): Account
    {
        $originAccountId = $this->event->destination;
        return $this->getAccount($originAccountId);
    }
}
