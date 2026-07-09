<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\GradeInput;
use App\Enums\LetterGrade;

interface GradeCalculatorContract
{
    public function totalScore(GradeInput $input): float;

    public function letterGrade(float $score): LetterGrade;

    /** @return array{total_score: float, letter_grade: LetterGrade, gpa_points: float} */
    public function fullResult(GradeInput $input): array;
}
