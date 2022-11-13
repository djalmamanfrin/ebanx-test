<?php

namespace Tests\Services;

use App\Enums\TypesEnum;
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

    private Account $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = Account::factory()->create();
    }

    public function test_whether_abstract_transaction_service_methods_are_available_in_the_deposit_service()
    {
        $this->assertTrue(method_exists(DepositService::class, 'checkingMinimumAllowed'));
        $this->assertTrue(method_exists(DepositService::class, 'persist'));
    }

    public function test_expecting_error_in_persist_method_whether_amount_less_than_minimum_allowed()
    {
        $amount = 0;
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed must be greater than or equal to %s";
        $this->expectExceptionMessage(sprintf($message, $amount, TransactionService::MINIMUM_ALLOWED_VALUE));

        $event = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'origin' => $this->account->id,
            'amount' => $amount
        ]);
        $deposit = new DepositService($this->account, $event);
        $deposit->persist();
    }

    public function test_whether_persist_method_return_true_when_amount_is_equal_to_minimum_allowed()
    {
        $event = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'origin' => $this->account->id,
            'amount' => 1
        ]);
        $deposit = new DepositService($this->account, $event);
        $isDeposited = $deposit->persist();
        $this->assertTrue($isDeposited);
    }

    public function test_whether_persist_method_return_true_when_amount_greater_than_minimum_allowed()
    {
        $event = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'origin' => $this->account->id,
            'amount' => 2
        ]);
        $deposit = new DepositService($this->account, $event);
        $isDeposited = $deposit->persist();
        $this->assertTrue($isDeposited);
    }
}
