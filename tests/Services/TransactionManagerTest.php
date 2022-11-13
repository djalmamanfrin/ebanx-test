<?php

namespace Tests\Services;

use App\Models\Account;
use App\Services\TransactionManager;
use Tests\TestCase;

class TransactionManagerTest extends TestCase
{
    public function test_if_methods_are_available_in_the_transaction_manager()
    {
        $this->assertTrue(method_exists(TransactionManager::class, 'getOriginAccount'));
    }

    public function test_if_methods_return_expected_instance()
    {
        $manager = new TransactionManager();
        $this->assertInstanceOf(Account::class, $manager->getOriginAccount());
    }
}
