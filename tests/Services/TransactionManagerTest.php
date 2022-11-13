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

    private TransactionManager $account;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_if_methods_are_available_in_the_transaction_manager()
    {
        $this->assertTrue(method_exists(TransactionManager::class, 'getOriginAccount'));
    }

    public function test_expecting_error_in_get_origin_account_method_if_origin_non_existing()
    {
        $origin = 10;
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Account {$origin} not found");
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        $event = Event::factory()->make(['origin' => $origin]);
        $manager = new TransactionManager($event);
        $manager->getOriginAccount();
    }

    public function test_if_methods_return_expected_instance()
    {
        $account = Account::factory()->create();
        $event = Event::factory()->make(['origin' => $account->id]);
        $manager = new TransactionManager($event);
        $this->assertInstanceOf(Account::class, $manager->getOriginAccount());
    }
}
