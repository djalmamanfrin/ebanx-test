<?php

namespace App\Services;

use App\Models\Account;

class TransactionManager
{
    public function getOriginAccount(): Account
    {
        return new Account();
    }
}
