<?php

namespace Tests\Services;

use App\Models\Account;
use App\Services\AccountService;
use App\Services\DepositService;
use Tests\TestCase;

class DepositServiceTest extends TestCase
{
    public function test_is_the_minimum_allowed_method_exist()
    {
        $this->assertTrue(method_exists(DepositService::class, 'isTheMinimumAllowed'));
    }
}
