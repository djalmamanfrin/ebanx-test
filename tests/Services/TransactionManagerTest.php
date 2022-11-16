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

    public function test_expecting_error_to_withdraw_amount_if_origin_account_non_existing()
    {
        $origin = 10;
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Account {$origin} not found");
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        $manager = new TransactionManager();
        $manager->persist(['type' => TypesEnum::withdraw(), 'origin' => $origin, 'amount' => 10]);
    }

    public function test_expecting_success_to_deposit_amount_when_existing_account()
    {
        $amount = 100;
        $destinationAccountId = Account::factory()->create()->id;
        $manager = new TransactionManager();


        $manager->persist(['type' => TypesEnum::deposit(), 'destination' => $destinationAccountId, 'amount' => $amount]);
        $this->assertEquals($amount, $manager->getBalance($destinationAccountId));
    }

    public function test_expecting_success_to_deposit_amount_when_non_existing_account()
    {
        $amount = 100;
        $accountId = 2;

        $manager = new TransactionManager();

        $manager->persist(['type' => TypesEnum::deposit(), 'destination' => $accountId, 'amount' => $amount]);
        $this->assertEquals($amount, $manager->getBalance($accountId));
    }

    public function test_expecting_error_to_withdraw_amount_without_balance_in_account()
    {
        $amount = 100;
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed is greater than balance 0 in account";
        $this->expectExceptionMessage(sprintf($message, $amount));

        $originAccountId = Account::factory()->create()->id;
        $manager = new TransactionManager();
        $manager->persist(['type' => TypesEnum::withdraw(), 'origin' => $originAccountId, 'amount' => $amount]);
    }

    public function test_expecting_error_to_withdraw_amount_when_greater_than_balance_in_account()
    {
        $withdrawAmount = 100;
        $depositAmount = 10;
        $accountId = 2;

        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed is greater than balance %s in account";
        $this->expectExceptionMessage(sprintf($message, $withdrawAmount, $depositAmount));

        $manager = new TransactionManager();

        $manager->persist(['type' => TypesEnum::deposit(), 'destination' => $accountId, 'amount' => $depositAmount]);
        $this->assertEquals($depositAmount, $manager->getBalance($accountId));

        $manager->persist(['type' => TypesEnum::withdraw(), 'origin' => $accountId, 'amount' => $withdrawAmount]);
    }

    public function test_expecting_success_to_withdraw_amount_when_less_than_balance_in_account()
    {
        $withdrawAmount = 99;
        $depositAmount = 100;
        $accountId = 100;

        $manager = new TransactionManager();

        $manager->persist(['type' => TypesEnum::deposit(), 'destination' => $accountId, 'amount' => $depositAmount]);
        $this->assertEquals($depositAmount, $manager->getBalance($accountId));

        $manager->persist(['type' => TypesEnum::withdraw(), 'origin' => $accountId, 'amount' => $withdrawAmount]);
        $this->assertEquals(1, $manager->getBalance($accountId));
    }


    public function test_expecting_error_to_transfer_amount_without_balance_in_account()
    {
        $amount = 25;
        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed is greater than balance 0 in account";
        $this->expectExceptionMessage(sprintf($message, $amount));

        $accountId = Account::factory()->create()->id;
        $manager = new TransactionManager();
        $manager->persist(['type' => TypesEnum::transfer(), 'origin' => $accountId, 'destination' => 200, 'amount' => $amount]);
    }

    public function test_expecting_error_to_transfer_amount_when_greater_than_balance_in_account()
    {
        $transferAmount = 100;
        $depositAmount = 25;

        $this->expectException(InvalidArgumentException::class);
        $message = "The amount %s informed is greater than balance %s in account";
        $this->expectExceptionMessage(sprintf($message, $transferAmount, $depositAmount));

        $manager = new TransactionManager();
        $manager->persist(['type' => TypesEnum::deposit(), 'destination' => 100, 'amount' => $depositAmount]);
        $this->assertEquals($depositAmount, $manager->getBalance(100));

        $manager->persist(['type' => TypesEnum::transfer(), 'origin' => 100, 'destination' => 200, 'amount' => $transferAmount]);
    }

    public function test_expecting_success_to_transfer_amount_when_less_than_balance_in_account()
    {
        $manager = new TransactionManager();

        $manager->persist(['type' => TypesEnum::deposit(), 'destination' => 100, 'amount' => 100]);
        $this->assertEquals(100, $manager->getBalance(100));

        $manager->persist(['type' => TypesEnum::withdraw(), 'origin' => 100, 'amount' => 75]);
        $this->assertEquals(25, $manager->getBalance(100));

        $manager->persist(['type' => TypesEnum::transfer(), 'origin' => 100, 'destination' => 200, 'amount' => 10]);
        $this->assertEquals(15, $manager->getBalance(100));
        $this->assertEquals(10, $manager->getBalance(200));
    }
}
