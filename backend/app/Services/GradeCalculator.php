<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GradeCalculatorContract;
use App\DTOs\GradeInput;
use App\Enums\LetterGrade;
use App\Models\Grade;

final class GradeCalculator implements GradeCalculatorContract
{
    /** @var array<string,int> */
    private array $weights;

    public function __construct()
    {
        $this->weights = config('srms.grade_weights', Grade::WEIGHTS);
    }

    public function totalScore(GradeInput $input): float
    {
        $data = $input->toArray();
        $sum = 0.0;
        $totalWeight = 0;
        foreach ($this->weights as $key => $w) {
            $sum += ($data[$key] ?? 0) * $w;
            $totalWeight += $w;
        }
        return $totalWeight > 0 ? round($sum / $totalWeight, 2) : 0.0;
    }

    public function letterGrade(float $score): LetterGrade
    {
        return LetterGrade::fromScore($score);
    }

    public function fullResult(GradeInput $input): array
    {
        $score = $this->totalScore($input);
        $letter = $this->letterGrade($score);
        return [
            'total_score' => $score,
            'letter_grade' => $letter,
            'gpa_points' => $letter->point(),
        ];
    }
}
