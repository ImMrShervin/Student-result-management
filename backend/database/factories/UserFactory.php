<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'phone' => $this->faker->phoneNumber(),
            'national_id' => $this->faker->unique()->numerify('##########'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'birth_date' => $this->faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
            'address' => $this->faker->address(),
            'email_verified_at' => now(),
            'is_active' => true,
            'locale' => 'en',
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
