<?php

namespace Tests\Services;

use App\Models\Account;
use App\Models\Event;
use App\Services\DepositService;
use App\Services\TransactionService;
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

    public function test_methods_exist_in_deposit_service()
    {
        $this->assertTrue(method_exists(DepositService::class, 'isTheMinimumAllowed'));
        $this->assertTrue(method_exists(DepositService::class, 'setAmount'));
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
        $message = "The deposit amount informed must be greater than or equal to" . TransactionService::MINIMUM_ALLOWED_VALUE;
        $this->expectExceptionMessage($message);
        $this->deposit
            ->setAmount(0)
            ->persist();
    }

    public function test_success_in_depositing_value_is_equal_one()
    {
        $isDeposited = $this->deposit
            ->setAmount(1)
            ->persist();
        $this->assertTrue($isDeposited);
    }

    public function test_success_in_depositing_value_is_greater_than_one()
    {
        $isDeposited = $this->deposit
            ->setAmount(2)
            ->persist();
        $this->assertTrue($isDeposited);
    }
}
