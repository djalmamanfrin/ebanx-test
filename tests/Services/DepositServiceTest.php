<?php

namespace Tests\Services;

use App\Models\Account;
use App\Models\Event;
use App\Services\DepositService;
use App\Services\TransactionService;
use App\TypesEnum;
use InvalidArgumentException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class DepositServiceTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    private DepositService $deposit;

    public function setUp(): void
    {
        parent::setUp();
        $account = Account::factory()->make();
        $event = Event::factory()->make();
        $this->deposit = new DepositService($account, $event);
    }

    public function test_whether_abstract_transaction_service_methods_are_available_in_the_deposit_service()
    {
        $this->assertTrue(method_exists(DepositService::class, 'checkingMinimumAllowed'));
        $this->assertTrue(method_exists(DepositService::class, 'persist'));
    }

    public function test_return_is_the_minimum_allowed_method_is_boolean()
    {
        $amount = 0;
        $result = $this->invokeNonPublicMethod($this->deposit, 'isTheMinimumAllowed', [$amount]);
        $this->assertIsBool($result);
    }

    public function test_is_the_minimum_allowed_method_not_acceptable_amount_equal_zero()
    {
        $amount = 0;
        $result = $this->invokeNonPublicMethod($this->deposit, 'isTheMinimumAllowed', [$amount]);
        $this->assertFalse($result);
    }

    public function test_is_the_minimum_allowed_method_acceptable_amount_equal_one()
    {
        $amount = 1;
        $result = $this->invokeNonPublicMethod($this->deposit, 'isTheMinimumAllowed', [$amount]);
        $this->assertTrue($result);
    }

    public function test_is_the_minimum_allowed_method_acceptable_amount_gather_than_one()
    {
        $amount = 2;
        $result = $this->invokeNonPublicMethod($this->deposit, 'isTheMinimumAllowed', [$amount]);
        $this->assertTrue($result);
    }

    public function test_error_in_depositing_value_is_equal_zero()
    {
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount informed must be greater than or equal to" . TransactionService::MINIMUM_ALLOWED_VALUE;
        $this->expectExceptionMessage($message);

        $account = Account::factory()->create();
        $event = Event::factory()->create(['type' => 'deposit', 'origin' => $account->id, 'amount' => 0]);
        $deposit = new DepositService($account, $event);
        $deposit->persist();
    }

    public function test_success_in_depositing_value_is_equal_one()
    {
        $account = Account::factory()->create();
        $event = Event::factory()->create(['type' => 'deposit', 'origin' => $account->id, 'amount' => 1]);
        $deposit = new DepositService($account, $event);
        $isDeposited = $deposit->persist();
        $this->assertTrue($isDeposited);
    }

    public function test_success_in_depositing_value_is_greater_than_one()
    {
        $account = Account::factory()->create();
        $event = Event::factory()->create(['type' => 'deposit', 'origin' => $account->id, 'amount' => 2]);
        $deposit = new DepositService($account, $event);
        $isDeposited = $deposit->persist();
        $this->assertTrue($isDeposited);
    }
}
