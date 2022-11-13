<?php

namespace Tests\Services;

use App\Models\Account;
use App\Models\Event;
use App\Services\DepositService;
use App\Services\TransactionService;
use App\Services\WithdrawService;
use App\TypesEnum;
use InvalidArgumentException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class WithdrawServiceTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    private DepositService $deposit;
    private Account $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = Account::factory()->create();
        $event = Event::factory()->create();
        $this->withdraw = new WithdrawService($this->account, $event);
    }

    public function test_whether_abstract_transaction_service_methods_are_available_in_the_withdraw_service()
    {
        $this->assertTrue(method_exists(WithdrawService::class, 'checkingMinimumAllowed'));
        $this->assertTrue(method_exists(WithdrawService::class, 'persist'));
    }

    public function test_whether_has_found_method_is_available_in_the_withdraw_service()
    {
        $this->assertTrue(method_exists(WithdrawService::class, 'hasFound'));
    }

    public function test_expecting_error_in_has_found_method_whether_amount_less_than_minimum_allowed()
    {
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount informed must be greater than or equal to" . TransactionService::MINIMUM_ALLOWED_VALUE;
        $this->expectExceptionMessage($message);
        $this->withdraw->hasFound(0);
    }

    public function test_return_has_found_method_is_boolean()
    {
        $amount = 1;
        $this->assertIsBool($this->withdraw->hasFound($amount));
    }

    public function test_whether_has_found_method_return_false_when_amount_greater_than_balance()
    {
        $amount = 1;
        $this->assertFalse($this->withdraw->hasFound($amount));
    }

    public function test_whether_has_found_method_return_true_when_amount_less_than_balance()
    {
        $amount = 8;
        $event = Event::factory()->create(['type' => 'deposit', 'origin' => $this->account->id]);
        $deposit = new DepositService($this->account, $event);
        $deposit->persist();
        $this->assertTrue($this->withdraw->hasFound($amount));
    }

    public function test_whether_has_found_method_return_true_when_amount_equal_to_balance()
    {
        $amount = 10;
        $event = Event::factory()->create(['type' => TypesEnum::deposit(), 'origin' => $this->account->id]);
        $deposit = new DepositService($this->account, $event);
        $deposit->persist();
        $this->assertTrue($this->withdraw->hasFound($amount));
    }
}
