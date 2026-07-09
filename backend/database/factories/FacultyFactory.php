<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyFactory extends Factory
{
    protected $model = Faculty::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Engineering', 'Science', 'Medical', 'Business',
            'Humanities', 'Law', 'Arts', 'Agriculture',
        ]);
        return [
            'name' => "Faculty of {$name}",
            'code' => strtoupper(substr($name, 0, 3)),
            'description' => $this->faker->sentence(),
        ];
    }
}
