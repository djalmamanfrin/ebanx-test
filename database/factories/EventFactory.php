<?php

namespace Database\Factories;

use App\Enums\TypesEnum;
use App\Models\Event;
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
        $type = $this->faker->randomElement(TypesEnum::slugs());
        $destination = $type !== TypesEnum::deposit()
            ? $this->faker->numberBetween(1, 10)
            : null;

        return [
            'type' => $type,
            'origin' => $this->faker->numberBetween(1, 10),
            'destination' => $destination,
            'amount' => $this->faker->numberBetween(10, 100)
        ];
    }
}
