<?php

namespace Tests\Services;

use App\Enums\TypesEnum;
use App\Models\Account;
use App\Models\Event;
use App\Services\DepositService;
use App\Services\TransactionService;
use App\Services\TransferService;
use InvalidArgumentException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    private TransferService $transfer;
    private Account $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = Account::factory()->create();
        $event = Event::factory()->create();
        $this->transfer = new TransferService($this->account, $event);
    }

    public function test_whether_abstract_transaction_service_methods_are_available_in_the_transfer_service()
    {
        $this->assertTrue(method_exists(TransferService::class, 'checkingMinimumAllowed'));
        $this->assertTrue(method_exists(TransferService::class, 'persist'));
    }

    public function test_whether_has_found_method_is_available_in_the_transfer_service()
    {
        $this->assertTrue(method_exists(TransferService::class, 'hasFund'));
    }

    public function test_expecting_error_in_has_found_method_whether_amount_less_than_minimum_allowed()
    {
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount informed must be greater than or equal to" . TransactionService::MINIMUM_ALLOWED_VALUE;
        $this->expectExceptionMessage($message);
        $this->transfer->hasFund(0);
    }

    public function test_if_has_found_method_return_type_is_boolean()
    {
        $amount = 1;
        $this->assertIsBool($this->transfer->hasFund($amount));
    }

    public function test_if_has_found_method_return_false_when_amount_greater_than_minimum_allowed()
    {
        $amount = 1;
        $this->assertFalse($this->transfer->hasFund($amount));
    }

    public function test_if_has_found_method_return_true_when_amount_less_than_minimum_allowed()
    {
        $amount = 8;
        $event = Event::factory()->create(['type' => TypesEnum::deposit(), 'origin' => $this->account->id]);
        $deposit = new DepositService($this->account, $event);
        $deposit->persist();
        $this->assertTrue($this->transfer->hasFund($amount));
    }

    public function test_if_has_found_method_return_true_when_amount_equal_to_minimum_allowed()
    {
        $amount = 10;
        $event = Event::factory()->create(['type' => TypesEnum::deposit(), 'origin' => $this->account->id]);
        $deposit = new DepositService($this->account, $event);
        $deposit->persist();
        $this->assertTrue($this->transfer->hasFund($amount));
    }
}
