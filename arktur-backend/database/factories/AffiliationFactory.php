<?php

namespace Database\Factories;

use App\Models\Affiliation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AffiliationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Affiliation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->streetName()
        ];
    }
}
