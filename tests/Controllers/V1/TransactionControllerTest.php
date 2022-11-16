<?php

namespace Tests\Controllers\V1;

use App\Enums\TypesEnum;
use App\Http\Controllers\V1\TransactionController;
use App\Models\Account;
use App\Models\Event;
use App\Services\TransactionManager;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    public function test_if_methods_are_available_in_the_transaction_controller()
    {
        $this->assertTrue(method_exists(TransactionController::class, 'event'));
        $this->assertTrue(method_exists(TransactionController::class, 'balance'));
    }

    public function test_create_account_with_initial_balance()
    {
        $data = [
            'type' => TypesEnum::deposit(),
            'destination' => 100,
            'amount' => 10
        ];
        $response = $this
            ->call('POST', '/event', $data);
        $response->assertExactJson([
            'destination' => [
                'id' => 100,
                'balance' => 10
            ]
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_deposit_into_existing_account()
    {
        $accountId = Account::factory()->create()->id;
        $data = [
            'type' => TypesEnum::deposit(),
            'destination' => $accountId,
            'amount' => 10
        ];
        $response = $this
            ->call('POST', '/event', $data);
        $response->assertExactJson([
            'destination' => [
                'id' => $accountId,
                'balance' => 10
            ]
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_get_balance_for_non_existing_account()
    {
        $accountId = 100;

        $response = $this
            ->call('GET', "/balance?account_id=$accountId");
        $response->assertExactJson([0]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_get_balance_for_existing_account()
    {
        $amount = 27.77;
        $accountId = 100;
        $manager = new TransactionManager();
        $manager->persist(['type' => TypesEnum::deposit(), 'destination' => $accountId, 'amount' => $amount]);

        $response = $this
            ->call('GET', "/balance?account_id=$accountId");
        $response->assertExactJson([$amount]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_withdraw_from_non_existing_account()
    {
        $data = [
            'type' => TypesEnum::withdraw(),
            'origin' => 100,
            'amount' => 10
        ];
        $response = $this
            ->call('POST', '/event', $data);
        $response->assertExactJson([0]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_withdraw_from_existing_account()
    {
        $amount = 27.77;
        $accountId = 100;
        $manager = new TransactionManager();
        $manager->persist(['type' => TypesEnum::deposit(), 'destination' => $accountId, 'amount' => $amount]);

        $data = [
            'type' => TypesEnum::withdraw(),
            'origin' => $accountId,
            'amount' => 10
        ];
        $response = $this
            ->call('POST', '/event', $data);
        $response->assertExactJson([
            'origin' => [
                'id' => $accountId,
                'balance' => 17.77
            ]
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_transfer_from_non_existing_account()
    {
        $data = [
            'type' => TypesEnum::transfer(),
            'origin' => 100,
            'amount' => 10
        ];
        $response = $this
            ->call('POST', '/event', $data);
        $response->assertExactJson([0]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_transfer_from_existing_account()
    {
        $amount = 100;
        $originAccountId = 100;
        $destinationAccountId = 200;
        $manager = new TransactionManager();
        $manager->persist(['type' => TypesEnum::deposit(), 'destination' => $originAccountId, 'amount' => $amount]);

        $data = [
            'type' => TypesEnum::transfer(),
            'origin' => $originAccountId,
            'destination' => $destinationAccountId,
            'amount' => 55.55
        ];
        $response = $this
            ->call('POST', '/event', $data);
        $response->assertExactJson([
            'origin' => [
                'id' => $originAccountId,
                'balance' => 44.45
            ],
            'destination' => [
                'id' => $destinationAccountId,
                'balance' => 55.55
            ]
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
    }
}
