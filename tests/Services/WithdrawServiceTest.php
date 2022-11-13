<?php

namespace Tests\Services;

use App\Services\WithdrawService;
use Tests\TestCase;

class WithdrawServiceTest extends TestCase
{
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
}
