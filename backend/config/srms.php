<?php

declare(strict_types=1);

return [
    'grade_weights' => [
        'attendance' => 5,
        'assignment' => 10,
        'quiz'       => 10,
        'project'    => 10,
        'midterm'    => 20,
        'practical'  => 10,
        'final_exam' => 35,
    ],
    'max_credits_per_semester' => 21,
    'default_credits_required' => 140,
    'gpa_scale' => 4.0,
    'university' => [
        'name' => env('UNIVERSITY_NAME', 'Genspark University'),
        'verify_url' => env('UNIVERSITY_TRANSCRIPT_VERIFY_URL'),
    ],
];
