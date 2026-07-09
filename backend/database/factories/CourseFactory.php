<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'code' => strtoupper($this->faker->unique()->bothify('??###')),
            'title' => ucwords($this->faker->words(3, true)),
            'description' => $this->faker->paragraph(),
            'theory_credit' => $this->faker->numberBetween(2, 4),
            'practical_credit' => $this->faker->numberBetween(0, 2),
        ];
    }
}
