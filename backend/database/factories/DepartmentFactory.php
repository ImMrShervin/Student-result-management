<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $names = ['Computer Engineering', 'Electrical Engineering', 'Civil Engineering', 'Mechanical Engineering',
            'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Medicine', 'Nursing', 'Business Administration',
            'Accounting', 'Economics', 'Law', 'Architecture'];
        return [
            'faculty_id' => Faculty::factory(),
            'name' => $this->faker->unique()->randomElement($names),
            'code' => strtoupper($this->faker->unique()->bothify('D##')),
            'description' => $this->faker->sentence(),
        ];
    }
}
