<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\StudentRepositoryContract;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private readonly StudentRepositoryContract $repo) {}

    public function index(Request $r)
    {
        $user = $r->user();

        return match (true) {
            $user->hasRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value]) => $this->admin(),
            $user->hasRole(UserRole::DEAN->value) => $this->dean(),
            $user->hasRole(UserRole::DEPARTMENT_HEAD->value) => $this->departmentHead($user),
            $user->hasRole(UserRole::TEACHER->value) => $this->teacher($user),
            $user->hasRole(UserRole::STUDENT->value) => $this->student($user),
            default => response()->json(['message' => 'no dashboard for role'], 403),
        };
    }

    private function admin(): \Illuminate\Http\JsonResponse
    {
        $data = Cache::remember('dashboard.admin', 300, function () {
            return [
                'stats' => [
                    'students' => Student::count(),
                    'teachers' => Teacher::count(),
                    'courses'  => Course::count(),
                    'faculties' => Faculty::count(),
                    'departments' => Department::count(),
                    'enrollments' => Enrollment::count(),
                    'published_grades' => Grade::where('is_published', true)->count(),
                ],
                'top_students' => $this->repo->topStudents(10),
                'avg_gpa_by_department' => $this->repo->averageGpaByDepartment(),
                'grade_distribution' => Grade::where('is_published', true)
                    ->select('letter_grade', DB::raw('COUNT(*) as c'))
                    ->groupBy('letter_grade')->get(),
                'academic_status_distribution' => Student::select('academic_status', DB::raw('COUNT(*) as c'))
                    ->groupBy('academic_status')->get(),
            ];
        });
        return response()->json($data);
    }

    private function dean(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'stats' => [
                'faculties' => Faculty::count(),
                'departments' => Department::count(),
                'students' => Student::count(),
            ],
            'avg_gpa_by_department' => $this->repo->averageGpaByDepartment(),
        ]);
    }

    private function departmentHead($user): \Illuminate\Http\JsonResponse
    {
        $dept = Department::where('head_id', $user->id)->first();
        if (! $dept) {
            return response()->json(['message' => 'You are not head of a department.'], 404);
        }
        return response()->json([
            'department' => $dept,
            'stats' => [
                'teachers' => Teacher::where('department_id', $dept->id)->count(),
                'students' => Student::where('department_id', $dept->id)->count(),
                'courses'  => Course::where('department_id', $dept->id)->count(),
            ],
        ]);
    }

    private function teacher($user): \Illuminate\Http\JsonResponse
    {
        $teacher = $user->teacher;
        $sections = $teacher?->sections()->with('course', 'semester')->get() ?? collect();
        return response()->json([
            'sections' => $sections,
            'stats' => [
                'sections' => $sections->count(),
                'students' => Enrollment::whereIn('course_section_id', $sections->pluck('id'))->distinct('student_id')->count('student_id'),
                'pending_grades' => Enrollment::whereIn('course_section_id', $sections->pluck('id'))
                    ->whereDoesntHave('grade', fn ($q) => $q->where('is_published', true))->count(),
            ],
        ]);
    }

    private function student($user): \Illuminate\Http\JsonResponse
    {
        $student = $user->student()->with(['department', 'faculty', 'semesterGpas.semester'])->first();
        $enrollments = $student?->enrollments()->with(['courseSection.course', 'grade', 'semester'])->latest()->limit(10)->get();
        return response()->json([
            'student' => $student,
            'recent_enrollments' => $enrollments,
        ]);
    }
}
