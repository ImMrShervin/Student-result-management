<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Exceptions\EnrollmentException;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

final class EnrollmentService
{
    public function __construct(private readonly int $maxCreditsPerSemester = 21) {}

    /**
     * @throws EnrollmentException
     */
    public function enroll(Student $student, CourseSection $section): Enrollment
    {
        return DB::transaction(function () use ($student, $section) {
            $section = CourseSection::lockForUpdate()->with('course.prerequisites')->findOrFail($section->id);

            $this->assertNotDuplicate($student, $section);
            $this->assertCapacity($section);
            $this->assertPrerequisitesMet($student, $section);
            $this->assertUnderCreditCap($student, $section);

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'course_section_id' => $section->id,
                'semester_id' => $section->semester_id,
                'status' => EnrollmentStatus::PENDING->value,
                'enrolled_at' => now(),
            ]);

            $section->increment('enrolled_count');

            return $enrollment;
        });
    }

    public function approve(Enrollment $e): Enrollment
    {
        $e->update(['status' => EnrollmentStatus::APPROVED->value, 'decided_at' => now()]);
        return $e;
    }

    public function reject(Enrollment $e): Enrollment
    {
        DB::transaction(function () use ($e) {
            $e->update(['status' => EnrollmentStatus::REJECTED->value, 'decided_at' => now()]);
            $e->courseSection()->decrement('enrolled_count');
        });
        return $e;
    }

    public function withdraw(Enrollment $e): Enrollment
    {
        DB::transaction(function () use ($e) {
            $e->update(['status' => EnrollmentStatus::WITHDRAWN->value, 'decided_at' => now()]);
            $e->courseSection()->decrement('enrolled_count');
        });
        return $e;
    }

    private function assertNotDuplicate(Student $student, CourseSection $section): void
    {
        $exists = Enrollment::where('student_id', $student->id)
            ->where('course_section_id', $section->id)
            ->whereNotIn('status', [EnrollmentStatus::REJECTED->value, EnrollmentStatus::WITHDRAWN->value])
            ->exists();
        if ($exists) {
            throw new EnrollmentException('Already enrolled in this section.');
        }
    }

    private function assertCapacity(CourseSection $section): void
    {
        if (! $section->hasCapacity()) {
            throw new EnrollmentException('Section is full.');
        }
    }

    private function assertPrerequisitesMet(Student $student, CourseSection $section): void
    {
        $prereqIds = $section->course->prerequisites->pluck('id');
        if ($prereqIds->isEmpty()) {
            return;
        }

        $passed = $student->enrollments()
            ->whereHas('courseSection', fn ($q) => $q->whereIn('course_id', $prereqIds))
            ->whereHas('grade', fn ($q) => $q->where('is_published', true)->where('gpa_points', '>', 0))
            ->with('courseSection')
            ->get()
            ->pluck('courseSection.course_id')
            ->unique();

        $missing = $prereqIds->diff($passed);
        if ($missing->isNotEmpty()) {
            throw new EnrollmentException('Missing prerequisite courses: ' . $missing->implode(', '));
        }
    }

    private function assertUnderCreditCap(Student $student, CourseSection $section): void
    {
        $creditsInSemester = Enrollment::where('student_id', $student->id)
            ->where('semester_id', $section->semester_id)
            ->whereNotIn('status', [EnrollmentStatus::REJECTED->value, EnrollmentStatus::WITHDRAWN->value])
            ->with('courseSection.course')
            ->get()
            ->sum(fn ($e) => (int) $e->courseSection->course->theory_credit + (int) $e->courseSection->course->practical_credit);

        $sectionCredits = (int) $section->course->theory_credit + (int) $section->course->practical_credit;

        if (($creditsInSemester + $sectionCredits) > $this->maxCreditsPerSemester) {
            throw new EnrollmentException("Enrollment exceeds max {$this->maxCreditsPerSemester} credits per semester.");
        }
    }
}
