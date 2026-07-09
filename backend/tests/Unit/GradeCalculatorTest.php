<?php

declare(strict_types=1);

use App\DTOs\GradeInput;
use App\Enums\LetterGrade;
use App\Services\GradeCalculator;

it('computes weighted total score correctly', function () {
    $calc = new GradeCalculator();
    $score = $calc->totalScore(new GradeInput(
        attendance: 100, assignment: 90, quiz: 85, project: 80,
        midterm: 88, practical: 90, finalExam: 92
    ));
    expect($score)->toBe(89.30);
});

it('maps score to correct letter grade', function () {
    $calc = new GradeCalculator();
    expect($calc->letterGrade(98))->toBe(LetterGrade::A_PLUS);
    expect($calc->letterGrade(85))->toBe(LetterGrade::B);
    expect($calc->letterGrade(55))->toBe(LetterGrade::F);
});

it('produces full result with letter and gpa points', function () {
    $calc = new GradeCalculator();
    $r = $calc->fullResult(new GradeInput(attendance:100,assignment:100,quiz:100,project:100,midterm:100,practical:100,finalExam:100));
    expect($r['total_score'])->toBe(100.0);
    expect($r['letter_grade'])->toBe(LetterGrade::A_PLUS);
    expect($r['gpa_points'])->toBe(4.00);
});
