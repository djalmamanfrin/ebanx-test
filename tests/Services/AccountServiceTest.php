<?php

namespace Tests\Services;

use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{
    use DatabaseMigrations;

    public function test_get_balance_method_exists()
    {
        $this->assertTrue(method_exists(AccountService::class, 'get'));
    }

    public function test_return_get_balance_method_must_be_int()
    {
        $account = new Account();
        $this->assertIsInt($account->getBalance());
    }

    public function test_exception_if_account_not_found()
    {
        $accountId = 1;
        $account = new AccountService($accountId);
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Account {$accountId} not found");
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $account->get();
    }
}
