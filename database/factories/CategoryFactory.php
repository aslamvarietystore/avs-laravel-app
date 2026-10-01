<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Hair Accessories',
            'Imitation Jewellery',
            'Toys',
            'Gift Items',
            'Kids Items',
            'Beauty Accessories',
            'Fans',
            'Other',
        ]);

        return [
            'parent_id' => null,
            'name' => $name,
            'slug' => str($name)->slug()->append('-'.Str::random(6))->toString(),
            'description' => $this->faker->sentence(),
            'status' => 'active',
        ];
    }

    /**
     * Turn the category into a sub-category of the given top-level category.
     */
    public function childOf(Category $parent): static
    {
        return $this->state(fn (): array => [
            'parent_id' => $parent->id,
        ]);
    }
}
