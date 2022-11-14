<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'event_id' => Event::factory()->make()->id,
            'account_id' => Account::factory()->make()->id,
            'amount' => $this->faker->numberBetween(10, 100)
        ];
    }
}
