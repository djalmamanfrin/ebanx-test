<?php

namespace Tests\Services;

use App\Models\Account;
use App\Models\Event;
use App\Services\TransactionManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TransactionManagerTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    public function test_if_methods_are_available_in_the_transaction_manager()
    {
        $this->assertTrue(method_exists(TransactionManager::class, 'getOriginAccount'));
        $this->assertTrue(method_exists(TransactionManager::class, 'getDestinationAccount'));
    }

    public function test_expecting_error_in_get_origin_account_method_if_account_non_existing()
    {
        $origin = 10;
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Account {$origin} not found");
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        $event = Event::factory()->make(['origin' => $origin]);
        $manager = new TransactionManager($event);
        $manager->getOriginAccount();
    }

    public function test_expecting_error_in_get_destination_account_method_if_account_non_existing()
    {
        $destination = 10;
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Account {$destination} not found");
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        $event = Event::factory()->make(['destination' => $destination]);
        $manager = new TransactionManager($event);
        $manager->getDestinationAccount();
    }

    public function test_if_origin_account_and_destination_account_methods_return_expected_instance()
    {
        $originAccount = Account::factory()->create();
        $destinationAccount = Account::factory()->create();
        $event = Event::factory()->make([
            'origin' => $originAccount->id,
            'destination' => $destinationAccount->id
        ]);
        $manager = new TransactionManager($event);
        $this->assertInstanceOf(Account::class, $manager->getOriginAccount());
        $this->assertInstanceOf(Account::class, $manager->getDestinationAccount());
    }
}
