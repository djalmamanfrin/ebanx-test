<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = Type::all()->pluck('slug')->toArray();
        return [
            'payload' => [
                'type' => $this->faker->randomElement($types),
                'origin'=> $this->faker->numberBetween(1, 10),
                'destination'=> $this->faker->numberBetween(1, 10),
                'amount' => $this->faker->numberBetween(10, 100),
            ]
        ];
    }
}
