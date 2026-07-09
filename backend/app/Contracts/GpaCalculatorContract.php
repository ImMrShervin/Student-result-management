<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Semester;
use App\Models\Student;

interface GpaCalculatorContract
{
    /** @return array{semester_gpa: float, credits_attempted: int, credits_earned: int} */
    public function semesterGpa(Student $student, Semester $semester): array;

    /** @return array{cumulative_gpa: float, credits_earned: int} */
    public function cumulativeGpa(Student $student): array;
}
