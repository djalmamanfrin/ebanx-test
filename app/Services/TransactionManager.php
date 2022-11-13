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

    public function getOriginAccount(): Account
    {
        $originAccountId = $this->event->origin;
        $service = new AccountService($originAccountId);
        return $service->get();
    }
}
