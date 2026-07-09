<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'faculty_id' => Faculty::factory(),
            'student_number' => 'S' . $this->faker->unique()->numerify('20########'),
            'entry_year' => $this->faker->numberBetween(2022, 2026),
            'current_semester' => $this->faker->numberBetween(1, 8),
            'current_gpa' => 0,
            'cumulative_gpa' => 0,
            'credits_passed' => 0,
            'credits_required' => 140,
            'academic_status' => 'passed',
        ];
    }
}
