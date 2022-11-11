<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class AccountService
{
    protected int $accountId;

    public function __construct(int $accountId)
    {
        $this->accountId = $accountId;
    }

    public function get(): Account
    {
        return Account::query()->findOr($this->accountId, function () {
            $message = "Account {$this->accountId} not found";
            throw new ModelNotFoundException($message, Response::HTTP_UNPROCESSABLE_ENTITY);
        });
    }
}
