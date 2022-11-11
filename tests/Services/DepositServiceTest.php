<?php

namespace Tests\Services;

use App\Models\Account;
use App\Services\DepositService;
use Tests\TestCase;

class DepositServiceTest extends TestCase
{
    public function test_is_the_minimum_allowed_method_exist()
    {
        $this->assertTrue(method_exists(DepositService::class, 'isTheMinimumAllowed'));
    }

    /**
     * @dataProvider account
     */
    public function test_return_is_the_minimum_allowed_method_is_boolean(Account $account)
    {
        $amount = 0;
        $deposit = new DepositService($account, $amount);
        $result = $this->invokeNonPublicMethod($deposit, 'isTheMinimumAllowed', [$amount]);
        $this->assertIsBool($result);
    }

    /**
     * @dataProvider account
     */
    public function test_is_the_minimum_allowed_method_not_acceptable_amount_equal_zero(Account $account)
    {
        $amount = 0;
        $deposit = new DepositService($account, $amount);
        $result = $this->invokeNonPublicMethod($deposit, 'isTheMinimumAllowed', [$amount]);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider account
     */
    public function test_is_the_minimum_allowed_method_acceptable_amount_equal_one(Account $account)
    {
        $amount = 1;
        $deposit = new DepositService($account, $amount);
        $result = $this->invokeNonPublicMethod($deposit, 'isTheMinimumAllowed', [$amount]);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider account
     */
    public function test_is_the_minimum_allowed_method_acceptable_amount_gather_than_one(Account $account)
    {
        $amount = 2;
        $deposit = new DepositService($account, $amount);
        $result = $this->invokeNonPublicMethod($deposit, 'isTheMinimumAllowed', [$amount]);
        $this->assertTrue($result);
    }

    public function account(): array
    {
        $account = Account::factory()->make();
        return [[$account]];
    }
}
