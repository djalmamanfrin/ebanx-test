<?php

namespace Tests\Services;

use App\Models\Account;
use App\Models\Event;
use App\Services\DepositService;
use App\Services\WithdrawService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class WithdrawServiceTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    private DepositService $deposit;

    public function setUp(): void
    {
        parent::setUp();
        $account = Account::factory()->make();
        $event = Event::factory()->make();
        $this->withdraw = new WithdrawService($account, $event);
    }

    public function test_whether_abstract_transaction_service_methods_are_available_in_the_withdraw_service()
    {
        $this->assertTrue(method_exists(WithdrawService::class, 'isTheMinimumAllowed'));
        $this->assertTrue(method_exists(WithdrawService::class, 'setAmount'));
        $this->assertTrue(method_exists(WithdrawService::class, 'persist'));
    }

    public function test_whether_has_found_method_is_available_in_the_withdraw_service()
    {
        $this->assertTrue(method_exists(WithdrawService::class, 'hasFound'));
    }

    public function test_return_has_found_method_is_boolean()
    {
        $amount = 0;
        $this->assertIsBool($this->withdraw->hasFound($amount));
    }

    public function test_whether_has_found_method_is_false_when_amount_greater_than_balance()
    {
        $amount = 1;
        $this->assertFalse($this->withdraw->hasFound($amount));
    }
}
