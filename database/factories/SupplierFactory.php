<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'contact_person' => $this->faker->name(),
            'phone' => $this->faker->numerify('03#########'),
            'email' => $this->faker->unique()->safeEmail(),
            'company_name' => $this->faker->company(),
            'city' => $this->faker->city(),
            'country' => 'Pakistan',
            'notes' => $this->faker->sentence(),
            'status' => 'active',
        ];
    }
}
