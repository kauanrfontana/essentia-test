<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'str_name' => fake()->name(),
            'str_email' => fake()->unique()->safeEmail(),
            'str_phone' => fake()->numerify('(##) #####-####'),
            'str_profile_picture_path' => 'customers/'.fake()->uuid().'.jpg',
        ];
    }

    /**
     * Customer without a stored profile picture.
     */
    public function withoutPicture(): static
    {
        return $this->state(fn () => ['str_profile_picture_path' => null]);
    }
}
