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

    public function test_whether_abstract_transaction_service_methods_are_available_in_the_deposit_service()
    {
        $this->assertTrue(method_exists(DepositService::class, 'checkingMinimumAllowed'));
        $this->assertTrue(method_exists(DepositService::class, 'persist'));
    }

    public function test_expecting_error_in_persist_method_whether_amount_less_than_minimum_allowed()
    {
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount informed must be greater than or equal to" . TransactionService::MINIMUM_ALLOWED_VALUE;
        $this->expectExceptionMessage($message);

        $account = Account::factory()->create();
        $event = Event::factory()->create(['type' => 'deposit', 'origin' => $account->id, 'amount' => 0]);
        $deposit = new DepositService($account, $event);
        $deposit->persist();
    }

    public function test_whether_persist_method_return_true_when_amount_is_equal_to_minimum_allowed()
    {
        $account = Account::factory()->create();
        $event = Event::factory()->create(['type' => 'deposit', 'origin' => $account->id, 'amount' => 1]);
        $deposit = new DepositService($account, $event);
        $isDeposited = $deposit->persist();
        $this->assertTrue($isDeposited);
    }

    public function test_whether_persist_method_return_true_when_amount_greater_than_minimum_allowed()
    {
        $account = Account::factory()->create();
        $event = Event::factory()->create(['type' => 'deposit', 'origin' => $account->id, 'amount' => 2]);
        $deposit = new DepositService($account, $event);
        $isDeposited = $deposit->persist();
        $this->assertTrue($isDeposited);
    }
}
