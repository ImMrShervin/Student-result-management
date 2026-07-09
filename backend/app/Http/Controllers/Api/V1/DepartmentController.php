<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $r)
    {
        return DepartmentResource::collection(
            Department::with('faculty')
                ->when($r->faculty_id, fn ($q, $v) => $q->where('faculty_id', $v))
                ->when($r->q, fn ($q, $v) => $q->where('name', 'like', "%$v%"))
                ->orderBy('name')
                ->paginate($r->integer('per_page', 15))
        );
    }

    public function store(Request $r)
    {
        $this->authorize('create', Department::class);
        $data = $r->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:120',
            'code' => 'required|string|max:16|unique:departments,code',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
        ]);
        return new DepartmentResource(Department::create($data));
    }

    public function show(Department $department)
    {
        return new DepartmentResource($department->load('faculty'));
    }

    public function update(Request $r, Department $department)
    {
        $this->authorize('update', $department);
        $data = $r->validate([
            'faculty_id' => 'sometimes|exists:faculties,id',
            'name' => 'sometimes|string|max:120',
            'code' => 'sometimes|string|max:16|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
        ]);
        $department->update($data);
        return new DepartmentResource($department);
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);
        $department->delete();
        return response()->noContent();
    }
}
