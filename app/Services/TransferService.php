<?php

namespace App\Services;

use App\Traits\CheckBalance;

class TransferService extends TransactionService
{
    use CheckBalance;

    public function persist(): bool
    {
        $this->checkingHasFund();
        return parent::persist();
    }
}
