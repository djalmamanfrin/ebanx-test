<?php

namespace Tests\Services;

use App\Models\Account;
use App\Models\Event;
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

    public function test_whether_has_found_method_is_available_in_the_withdraw_service()
    {
        $this->assertTrue(method_exists(TransferService::class, 'hasFound'));
    }

    public function test_expecting_error_in_has_found_method_whether_amount_less_than_minimum_allowed()
    {
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount informed must be greater than or equal to" . TransactionService::MINIMUM_ALLOWED_VALUE;
        $this->expectExceptionMessage($message);
        $this->transfer->hasFound(0);
    }
}
