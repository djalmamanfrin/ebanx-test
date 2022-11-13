<?php

namespace Tests\Services;

use App\Services\TransferService;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    public function test_whether_abstract_transaction_service_methods_are_available_in_the_transfer_service()
    {
        $this->assertTrue(method_exists(TransferService::class, 'checkingMinimumAllowed'));
        $this->assertTrue(method_exists(TransferService::class, 'persist'));
    }
}
