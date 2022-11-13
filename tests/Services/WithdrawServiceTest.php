<?php

namespace Tests\Services;

use App\Enums\TypesEnum;
use App\Models\Account;
use App\Models\Event;
use App\Services\DepositService;
use App\Services\WithdrawService;
use InvalidArgumentException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class WithdrawServiceTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    private Account $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = Account::factory()->create();
    }

    public function test_whether_abstract_transaction_service_methods_are_available_in_the_withdraw_service()
    {
        $this->assertTrue(method_exists(WithdrawService::class, 'checkingMinimumAllowed'));
        $this->assertTrue(method_exists(WithdrawService::class, 'persist'));
    }

    public function test_whether_has_found_method_is_available_in_the_withdraw_service()
    {
        $this->assertTrue(method_exists(WithdrawService::class, 'checkingHasFund'));
    }

    public function test_expecting_error_in_has_found_method_whether_amount_less_than_minimum_allowed()
    {
        $amount = 5;

        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed is greater than balance 0 in account";
        $this->expectExceptionMessage(sprintf($message, $amount));

        $event = Event::factory()->create([
            'type' => TypesEnum::withdraw(),
            'origin' => $this->account->id,
            'amount' => $amount
        ]);
        $withdraw = new WithdrawService($this->account, $event);
        $withdraw->checkingHasFund();
    }

    public function test_expecting_error_if_amount_withdrawn_greater_than_balance()
    {
        $depositAmount = 5;
        $withdrawAmount = 10;
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The amount $withdrawAmount informed is greater than balance $depositAmount in account");

        $event = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'origin' => $this->account->id,
            'amount' => $depositAmount
        ]);
        $deposit = new DepositService($this->account, $event);
        $this->assertTrue($deposit->persist());

        $event = Event::factory()->create([
            'type' => TypesEnum::withdraw(),
            'origin' => $this->account->id,
            'amount' => $withdrawAmount
        ]);
        $withdraw = new WithdrawService($this->account, $event);
        $withdraw->persist();
    }

    public function test_expecting_success_if_amount_withdrawn_equal_to_balance()
    {
        $event = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'origin' => $this->account->id,
            'amount' => 5
        ]);
        $deposit = new DepositService($this->account, $event);
        $this->assertTrue($deposit->persist());

        $event = Event::factory()->create([
            'type' => TypesEnum::withdraw(),
            'origin' => $this->account->id,
            'amount' => 5
        ]);
        $withdraw = new WithdrawService($this->account, $event);
        $this->assertTrue($withdraw->persist());
    }

    public function test_expecting_success_if_amount_withdrawn_less_than_balance()
    {
        $event = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'origin' => $this->account->id,
            'amount' => 10
        ]);
        $deposit = new DepositService($this->account, $event);
        $this->assertTrue($deposit->persist());

        $event = Event::factory()->create([
            'type' => TypesEnum::withdraw(),
            'origin' => $this->account->id,
            'amount' => 5
        ]);
        $withdraw = new WithdrawService($this->account, $event);
        $this->assertTrue($withdraw->persist());
    }
}
