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

class WithdrawServiceTest extends TestCase
{
    public function test_whether_abstract_transaction_service_methods_are_available_in_the_withdraw_service()
    {
        $this->assertTrue(method_exists(TransactionService::class, 'isTheMinimumAllowed'));
        $this->assertTrue(method_exists(TransactionService::class, 'setAmount'));
        $this->assertTrue(method_exists(TransactionService::class, 'persist'));
    }
}
