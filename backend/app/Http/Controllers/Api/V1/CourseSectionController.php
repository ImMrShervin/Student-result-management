<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CourseSection;
use Illuminate\Http\Request;

class CourseSectionController extends Controller
{
    public function index(Request $r)
    {
        return response()->json(
            CourseSection::with(['course.department', 'semester', 'teacher.user'])
                ->when($r->semester_id, fn ($q, $v) => $q->where('semester_id', $v))
                ->when($r->course_id, fn ($q, $v) => $q->where('course_id', $v))
                ->when($r->teacher_id, fn ($q, $v) => $q->where('teacher_id', $v))
                ->paginate($r->integer('per_page', 20))
        );
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'teacher_id' => 'required|exists:teachers,id',
            'section_code' => 'required|string|max:8',
            'capacity' => 'required|integer|min:1|max:500',
            'schedule' => 'nullable|string|max:120',
            'room' => 'nullable|string|max:32',
        ]);
        return response()->json(CourseSection::create($data), 201);
    }

    public function update(Request $r, CourseSection $section)
    {
        $data = $r->validate([
            'teacher_id' => 'sometimes|exists:teachers,id',
            'capacity' => 'sometimes|integer|min:1|max:500',
            'schedule' => 'nullable|string|max:120',
            'room' => 'nullable|string|max:32',
        ]);
        $section->update($data);
        return response()->json($section);
    }

    public function destroy(CourseSection $section)
    {
        $section->delete();
        return response()->noContent();
    }
}
