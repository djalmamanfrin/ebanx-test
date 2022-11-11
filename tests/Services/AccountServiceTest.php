<?php

namespace Tests\Services;

use App\Models\Account;
use App\Services\AccountService;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{
    /**
     * @depends test_account_service_class_exists
     */
    public function test_get_balance_method_exists()
    {
        $this->assertTrue(method_exists(AccountService::class, 'getBalance'));
    }

    /**
     * @dataProvider account
     */
    public function test_return_get_balance_method_must_be_int(AccountService $account)
    {
        $this->assertIsInt($account->getBalance());
    }

    public function account(): array
    {
        $account = new Account();
        return [[new AccountService($account)]];
    }
}
