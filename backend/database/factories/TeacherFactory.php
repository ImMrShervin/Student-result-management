<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Department;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'employee_code' => 'E' . $this->faker->unique()->numerify('#####'),
            'office' => 'Room ' . $this->faker->numberBetween(100, 599),
            'academic_rank' => $this->faker->randomElement(['lecturer', 'assistant_professor', 'associate_professor', 'professor']),
            'hired_on' => $this->faker->dateTimeBetween('-15 years', '-1 year')->format('Y-m-d'),
        ];
    }
}
