<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GpaCalculatorContract;
use App\Enums\AcademicStatus;
use App\Enums\EnrollmentStatus;
use App\Models\Semester;
use App\Models\SemesterGpa;
use App\Models\Student;

final class GpaCalculator implements GpaCalculatorContract
{
    public function semesterGpa(Student $student, Semester $semester): array
    {
        $rows = $student->enrollments()
            ->where('semester_id', $semester->id)
            ->whereIn('status', [EnrollmentStatus::APPROVED->value, EnrollmentStatus::COMPLETED->value])
            ->with(['grade', 'courseSection.course'])
            ->get();

        $totalPoints = 0.0;
        $attempted = 0;
        $earned = 0;

        foreach ($rows as $e) {
            if (! $e->grade || ! $e->grade->is_published) {
                continue;
            }
            $credits = (int) ($e->courseSection->course->theory_credit + $e->courseSection->course->practical_credit);
            $attempted += $credits;
            $totalPoints += (float) $e->grade->gpa_points * $credits;
            if ((float) $e->grade->gpa_points > 0) {
                $earned += $credits;
            }
        }

        $gpa = $attempted > 0 ? round($totalPoints / $attempted, 2) : 0.0;

        return [
            'semester_gpa' => $gpa,
            'credits_attempted' => $attempted,
            'credits_earned' => $earned,
        ];
    }

    public function cumulativeGpa(Student $student): array
    {
        $rows = $student->enrollments()
            ->whereIn('status', [EnrollmentStatus::APPROVED->value, EnrollmentStatus::COMPLETED->value])
            ->with(['grade', 'courseSection.course'])
            ->get();

        $totalPoints = 0.0;
        $totalCredits = 0;
        $earned = 0;

        foreach ($rows as $e) {
            if (! $e->grade || ! $e->grade->is_published) {
                continue;
            }
            $credits = (int) ($e->courseSection->course->theory_credit + $e->courseSection->course->practical_credit);
            $totalCredits += $credits;
            $totalPoints += (float) $e->grade->gpa_points * $credits;
            if ((float) $e->grade->gpa_points > 0) {
                $earned += $credits;
            }
        }

        return [
            'cumulative_gpa' => $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0,
            'credits_earned' => $earned,
        ];
    }

    public function snapshot(Student $student, Semester $semester): SemesterGpa
    {
        $sem = $this->semesterGpa($student, $semester);
        $cum = $this->cumulativeGpa($student);
        $status = self::deriveStatus($sem['semester_gpa'], $cum['cumulative_gpa'], $cum['credits_earned'], $student->credits_required);

        $snapshot = SemesterGpa::updateOrCreate(
            ['student_id' => $student->id, 'semester_id' => $semester->id],
            [
                'semester_gpa' => $sem['semester_gpa'],
                'cumulative_gpa' => $cum['cumulative_gpa'],
                'credits_attempted' => $sem['credits_attempted'],
                'credits_earned' => $sem['credits_earned'],
                'academic_status' => $status->value,
            ]
        );

        $student->forceFill([
            'current_gpa' => $sem['semester_gpa'],
            'cumulative_gpa' => $cum['cumulative_gpa'],
            'credits_passed' => $cum['credits_earned'],
            'academic_status' => $status->value,
        ])->save();

        return $snapshot;
    }

    public static function deriveStatus(float $semGpa, float $cumGpa, int $creditsEarned, int $creditsRequired): AcademicStatus
    {
        if ($creditsEarned >= $creditsRequired && $cumGpa >= 2.00) {
            return AcademicStatus::GRADUATED;
        }
        return match (true) {
            $cumGpa >= 3.75 => AcademicStatus::EXCELLENT,
            $cumGpa >= 2.00 => AcademicStatus::PASSED,
            $cumGpa >= 1.75 => AcademicStatus::CONDITIONAL,
            $cumGpa >= 1.50 => AcademicStatus::PROBATION,
            default          => AcademicStatus::FAILED,
        };
    }
}
