<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'AVS',
            'Bella',
            'Sunshine',
            'Royal Choice',
            'Kids Joy',
            'Home Style',
        ]).' '.$this->faker->unique()->numerify('##');

        return [
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'description' => $this->faker->sentence(),
            'status' => 'active',
        ];
    }
}
