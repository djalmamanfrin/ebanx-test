<?php

namespace App\Services;

use App\Traits\CheckBalance;

class WithdrawService extends TransactionService
{
    use CheckBalance;
}
