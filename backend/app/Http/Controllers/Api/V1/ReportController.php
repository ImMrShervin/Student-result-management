<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\StudentRepositoryContract;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct(private readonly StudentRepositoryContract $repo) {}

    public function topStudents(Request $r)
    {
        return StudentResource::collection($this->repo->topStudents($r->integer('limit', 10))->load(['user', 'department']));
    }

    public function failedStudents()
    {
        return StudentResource::collection(
            Student::with(['user', 'department'])->where('academic_status', 'failed')->paginate(20)
        );
    }

    public function excellentStudents()
    {
        return StudentResource::collection(
            Student::with(['user', 'department'])->where('academic_status', 'excellent')->orderByDesc('cumulative_gpa')->paginate(20)
        );
    }

    public function averageGpa()
    {
        return response()->json([
            'overall' => Student::avg('cumulative_gpa'),
            'by_department' => $this->repo->averageGpaByDepartment(),
        ]);
    }

    public function gradeDistribution(Request $r)
    {
        $q = Grade::query()->where('is_published', true);
        if ($r->section_id) {
            $q->whereHas('enrollment', fn ($e) => $e->where('course_section_id', $r->section_id));
        }
        return response()->json(
            $q->select('letter_grade', DB::raw('COUNT(*) as count'))->groupBy('letter_grade')->get()
        );
    }

    public function courseStats()
    {
        return response()->json(
            DB::table('courses')
                ->leftJoin('course_sections', 'courses.id', '=', 'course_sections.course_id')
                ->leftJoin('enrollments', 'course_sections.id', '=', 'enrollments.course_section_id')
                ->select('courses.code', 'courses.title',
                    DB::raw('COUNT(DISTINCT course_sections.id) as sections'),
                    DB::raw('COUNT(enrollments.id) as enrollments'))
                ->groupBy('courses.id', 'courses.code', 'courses.title')
                ->orderByDesc('enrollments')->limit(30)->get()
        );
    }

    public function departmentStats()
    {
        return response()->json(
            DB::table('departments')
                ->leftJoin('students', 'departments.id', '=', 'students.department_id')
                ->leftJoin('teachers', 'departments.id', '=', 'teachers.department_id')
                ->leftJoin('courses', 'departments.id', '=', 'courses.department_id')
                ->select('departments.name',
                    DB::raw('COUNT(DISTINCT students.id) as students'),
                    DB::raw('COUNT(DISTINCT teachers.id) as teachers'),
                    DB::raw('COUNT(DISTINCT courses.id) as courses'),
                    DB::raw('ROUND(AVG(students.cumulative_gpa),2) as avg_gpa'))
                ->groupBy('departments.id', 'departments.name')->get()
        );
    }

    public function enrollmentTrend()
    {
        return response()->json(
            Enrollment::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(*) as count'))
                ->groupBy('month')->orderBy('month')->get()
        );
    }

    public function passVsFail()
    {
        $pass = Grade::where('is_published', true)->where('gpa_points', '>', 0)->count();
        $fail = Grade::where('is_published', true)->where('gpa_points', 0)->count();
        return response()->json(compact('pass', 'fail'));
    }
}
