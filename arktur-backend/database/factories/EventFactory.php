<?php

namespace Database\Factories;

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
        return [
            'vnr' => random_int(10000, 99999),
            'semester' => random_int(20200, 20210),
            'title' => $this->faker->streetName(),
            'aktiv'=> $this->faker->boolean(),
            'sws' => $this->faker->randomDigit(),
            'type' => $this->faker->randomElement(['Übung', 'Seminar', 'Vorlesung', 'Tutorium']),
            'targets' => $this->faker->word(),
            'rythm' => random_int(0,2),
            'changed' => random_int(0,1)
        ];
    }
}
