<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GradeCalculatorContract;
use App\DTOs\GradeInput;
use App\Events\GradePublished;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;

final class GradeService
{
    public function __construct(
        private readonly GradeCalculatorContract $calculator,
        private readonly GpaCalculator $gpa,
    ) {}

    public function upsert(Enrollment $enrollment, GradeInput $input, int $graderId): Grade
    {
        return DB::transaction(function () use ($enrollment, $input, $graderId) {
            $result = $this->calculator->fullResult($input);

            return Grade::updateOrCreate(
                ['enrollment_id' => $enrollment->id],
                array_merge($input->toArray(), [
                    'total_score' => $result['total_score'],
                    'letter_grade' => $result['letter_grade']->value,
                    'gpa_points' => $result['gpa_points'],
                    'graded_by' => $graderId,
                ]),
            );
        });
    }

    public function publish(Grade $grade): Grade
    {
        DB::transaction(function () use ($grade) {
            $grade->update(['is_published' => true, 'published_at' => now()]);
            $enrollment = $grade->enrollment()->with(['student', 'semester'])->first();
            $this->gpa->snapshot($enrollment->student, $enrollment->semester);
            event(new GradePublished($grade));
        });
        return $grade->refresh();
    }
}
