<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnrollmentResource;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Student;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $service) {}

    public function index(Request $r)
    {
        $this->authorize('viewAny', Enrollment::class);
        return EnrollmentResource::collection(
            Enrollment::with(['student.user', 'courseSection.course', 'grade'])
                ->when($r->semester_id, fn ($q, $v) => $q->where('semester_id', $v))
                ->when($r->status, fn ($q, $v) => $q->where('status', $v))
                ->when($r->student_id, fn ($q, $v) => $q->where('student_id', $v))
                ->orderByDesc('id')
                ->paginate($r->integer('per_page', 20))
        );
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'student_id' => 'required|exists:students,id',
            'course_section_id' => 'required|exists:course_sections,id',
        ]);
        $student = Student::findOrFail($data['student_id']);
        $section = CourseSection::findOrFail($data['course_section_id']);
        $this->authorize('enroll', [$student]);
        $enrollment = $this->service->enroll($student, $section);
        return new EnrollmentResource($enrollment->load(['student.user', 'courseSection.course']));
    }

    public function approve(Enrollment $enrollment)
    {
        $this->authorize('decide', $enrollment);
        return new EnrollmentResource($this->service->approve($enrollment));
    }

    public function reject(Enrollment $enrollment)
    {
        $this->authorize('decide', $enrollment);
        return new EnrollmentResource($this->service->reject($enrollment));
    }

    public function withdraw(Enrollment $enrollment)
    {
        $this->authorize('withdraw', $enrollment);
        return new EnrollmentResource($this->service->withdraw($enrollment));
    }
}
