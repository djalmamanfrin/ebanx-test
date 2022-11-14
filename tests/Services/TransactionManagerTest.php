<?php

namespace Tests\Services;

use App\Enums\TypesEnum;
use App\Models\Account;
use App\Models\Event;
use App\Services\TransactionManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
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
        $this->assertTrue(method_exists(TransactionManager::class, 'persist'));
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

    public function test_expecting_success_to_deposit_amount_when_existing_account()
    {
        $amount = 100;
        $destinationAccountId = Account::factory()->create()->id;
        $event = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'destination' => $destinationAccountId,
            'amount' => $amount
        ]);
        $manager = new TransactionManager($event);
        $manager->persist();
        $this->assertEquals($amount, $manager->getBalance($destinationAccountId));
    }

    public function test_expecting_success_to_deposit_amount_when_non_existing_account()
    {
        $amount = 100;
        $destinationAccountId = 5;
        $event = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'destination' => $destinationAccountId,
            'amount' => $amount
        ]);
        $manager = new TransactionManager($event);
        $manager->persist();
        $this->assertEquals($amount, $manager->getBalance($destinationAccountId));
    }

    public function test_expecting_error_to_withdraw_amount_without_balance_in_account()
    {
        $amount = 100;
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed is greater than balance 0 in account";
        $this->expectExceptionMessage(sprintf($message, $amount));

        $originAccountId = Account::factory()->create()->id;
        $event = Event::factory()->create([
            'type' => TypesEnum::withdraw(),
            'origin' => $originAccountId,
            'amount' => $amount
        ]);
        $manager = new TransactionManager($event);
        $manager->persist();
    }

    public function test_expecting_error_to_withdraw_amount_when_greater_than_balance_in_account()
    {
        $withdrawAmount = 100;
        $depositAmount = 10;

        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed is greater than balance %s in account";
        $this->expectExceptionMessage(sprintf($message, $withdrawAmount, $depositAmount));

        $accountId = Account::factory()->create()->id;
        $depositEvent = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'destination' => $accountId,
            'amount' => $depositAmount
        ]);
        $manager = new TransactionManager($depositEvent);
        $manager->persist();

        $event = Event::factory()->create([
            'type' => TypesEnum::withdraw(),
            'origin' => $accountId,
            'amount' => $withdrawAmount
        ]);
        $manager = new TransactionManager($event);
        $manager->persist();
    }

    public function test_expecting_success_to_withdraw_amount_when_less_than_balance_in_account()
    {
        $withdrawAmount = 99;
        $depositAmount = 100;

        $accountId = Account::factory()->create()->id;
        $depositEvent = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'destination' => $accountId,
            'amount' => $depositAmount
        ]);
        $manager = new TransactionManager($depositEvent);
        $manager->persist();

        $event = Event::factory()->create([
            'type' => TypesEnum::withdraw(),
            'origin' => $accountId,
            'amount' => $withdrawAmount
        ]);
        $manager = new TransactionManager($event);
        $manager->persist();
        $this->assertEquals(1, $manager->getBalance($accountId));
    }


    public function test_expecting_error_to_transfer_amount_without_balance_in_account()
    {
        $amount = 25;
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed is greater than balance 0 in account";
        $this->expectExceptionMessage(sprintf($message, $amount));

        $accountId = Account::factory()->create()->id;
        $event = Event::factory()->create([
            'type' => TypesEnum::transfer(),
            'origin' => $accountId,
            'amount' => $amount
        ]);
        $manager = new TransactionManager($event);
        $manager->persist();
    }

    public function test_expecting_success_to_transfer_amount_when_greater_than_balance_in_account()
    {
        $transferAmount = 100;
        $depositAmount = 25;

        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed is greater than balance %s in account";
        $this->expectExceptionMessage(sprintf($message, $transferAmount, $depositAmount));

        $accountId = Account::factory()->create()->id;
        $depositEvent = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'destination' => $accountId,
            'amount' => $depositAmount
        ]);
        $manager = new TransactionManager($depositEvent);
        $manager->persist();

        $event = Event::factory()->create([
            'type' => TypesEnum::transfer(),
            'origin' => $accountId,
            'amount' => $transferAmount
        ]);
        $manager = new TransactionManager($event);
        $manager->persist();
        $this->assertEquals(75, $manager->getBalance($accountId));
    }

    public function test_expecting_success_to_transfer_amount_when_less_than_balance_in_account()
    {
        $transferAmount = 25;
        $depositAmount = 100;

        $DepositAccountId = Account::factory()->create()->id;
        $depositEvent = Event::factory()->create([
            'type' => TypesEnum::deposit(),
            'destination' => $DepositAccountId,
            'amount' => $depositAmount
        ]);
        $manager = new TransactionManager($depositEvent);
        $manager->persist();

        $transferAccountId = Account::factory()->create()->id;
        $transferEvent = Event::factory()->create([
            'type' => TypesEnum::transfer(),
            'origin' => $DepositAccountId,
            'destination' => $transferAccountId,
            'amount' => $transferAmount
        ]);
        $manager = new TransactionManager($transferEvent);
        $manager->persist();
        $this->assertEquals(75, $manager->getBalance($DepositAccountId));
        $this->assertEquals(25, $manager->getBalance($transferAccountId));
    }
}
