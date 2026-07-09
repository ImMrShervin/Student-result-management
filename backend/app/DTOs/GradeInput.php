<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class GradeInput
{
    public function __construct(
        public float $attendance = 0,
        public float $assignment = 0,
        public float $quiz = 0,
        public float $project = 0,
        public float $midterm = 0,
        public float $practical = 0,
        public float $finalExam = 0,
    ) {}

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            attendance: (float) ($data['attendance'] ?? 0),
            assignment: (float) ($data['assignment'] ?? 0),
            quiz:       (float) ($data['quiz'] ?? 0),
            project:    (float) ($data['project'] ?? 0),
            midterm:    (float) ($data['midterm'] ?? 0),
            practical:  (float) ($data['practical'] ?? 0),
            finalExam:  (float) ($data['final_exam'] ?? 0),
        );
    }

    /** @return array<string,float> */
    public function toArray(): array
    {
        return [
            'attendance' => $this->attendance,
            'assignment' => $this->assignment,
            'quiz' => $this->quiz,
            'project' => $this->project,
            'midterm' => $this->midterm,
            'practical' => $this->practical,
            'final_exam' => $this->finalExam,
        ];
    }
}
