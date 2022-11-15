<?php

namespace Tests\Controllers\V1;

use App\Enums\TypesEnum;
use App\Http\Controllers\V1\TransactionController;
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
}
