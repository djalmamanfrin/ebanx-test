<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Type;
use App\TypesEnum;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class DepositService
{
    const MINIMUM_ALLOWED_VALUE = 1;

    private Account $account;
    private float $amount;

    public function __construct(Account $account, float $amount)
    {
        $this->account = $account;
        $this->amount = $amount;
    }

    private function isTheMinimumAllowed(float $amount): bool
    {
        return $amount >= self::MINIMUM_ALLOWED_VALUE;
    }
}
