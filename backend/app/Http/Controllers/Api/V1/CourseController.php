<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $r)
    {
        return CourseResource::collection(
            Course::with('department')
                ->when($r->department_id, fn ($q, $v) => $q->where('department_id', $v))
                ->when($r->q, fn ($q, $v) => $q->where(fn ($qq) => $qq->where('code', 'like', "%$v%")->orWhere('title', 'like', "%$v%")))
                ->orderBy('code')
                ->paginate($r->integer('per_page', 20))
        );
    }

    public function store(Request $r)
    {
        $this->authorize('create', Course::class);
        $data = $r->validate([
            'department_id' => 'required|exists:departments,id',
            'code' => 'required|string|max:16|unique:courses,code',
            'title' => 'required|string|max:180',
            'description' => 'nullable|string',
            'theory_credit' => 'required|integer|min:0|max:6',
            'practical_credit' => 'required|integer|min:0|max:6',
            'prerequisite_ids' => 'array',
            'prerequisite_ids.*' => 'exists:courses,id',
        ]);
        $prereq = $data['prerequisite_ids'] ?? [];
        unset($data['prerequisite_ids']);
        $course = Course::create($data);
        if ($prereq) {
            $course->prerequisites()->sync($prereq);
        }
        return new CourseResource($course->load('prerequisites', 'department'));
    }

    public function show(Course $course)
    {
        return new CourseResource($course->load('prerequisites', 'department.faculty'));
    }

    public function update(Request $r, Course $course)
    {
        $this->authorize('update', $course);
        $data = $r->validate([
            'department_id' => 'sometimes|exists:departments,id',
            'code' => 'sometimes|string|max:16|unique:courses,code,' . $course->id,
            'title' => 'sometimes|string|max:180',
            'description' => 'nullable|string',
            'theory_credit' => 'sometimes|integer|min:0|max:6',
            'practical_credit' => 'sometimes|integer|min:0|max:6',
            'prerequisite_ids' => 'sometimes|array',
            'prerequisite_ids.*' => 'exists:courses,id',
        ]);
        $prereq = $data['prerequisite_ids'] ?? null;
        unset($data['prerequisite_ids']);
        $course->update($data);
        if ($prereq !== null) {
            $course->prerequisites()->sync($prereq);
        }
        return new CourseResource($course->load('prerequisites', 'department'));
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        $course->delete();
        return response()->noContent();
    }
}
