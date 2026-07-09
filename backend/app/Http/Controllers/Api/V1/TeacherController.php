<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $r)
    {
        $this->authorize('viewAny', Teacher::class);
        return TeacherResource::collection(
            Teacher::with(['user', 'department'])
                ->when($r->department_id, fn ($q, $v) => $q->where('department_id', $v))
                ->when($r->q, fn ($q, $v) => $q->whereHas('user', fn ($qu) => $qu
                    ->where('first_name', 'like', "%$v%")->orWhere('last_name', 'like', "%$v%")->orWhere('email', 'like', "%$v%")))
                ->paginate($r->integer('per_page', 15))
        );
    }

    public function store(Request $r)
    {
        $this->authorize('create', Teacher::class);
        $data = $r->validate([
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'nullable|string|max:32',
            'department_id' => 'required|exists:departments,id',
            'employee_code' => 'required|string|unique:teachers,employee_code',
            'office' => 'nullable|string|max:64',
            'academic_rank' => 'nullable|in:assistant_professor,associate_professor,professor,lecturer,instructor,adjunct',
            'hired_on' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'code' => $data['employee_code'],
                'is_active' => true,
            ]);
            $user->assignRole('teacher');
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'department_id' => $data['department_id'],
                'employee_code' => $data['employee_code'],
                'office' => $data['office'] ?? null,
                'academic_rank' => $data['academic_rank'] ?? 'lecturer',
                'hired_on' => $data['hired_on'] ?? null,
            ]);
            return new TeacherResource($teacher->load(['user', 'department']));
        });
    }

    public function show(Teacher $teacher)
    {
        $this->authorize('view', $teacher);
        return new TeacherResource($teacher->load(['user', 'department.faculty']));
    }

    public function update(Request $r, Teacher $teacher)
    {
        $this->authorize('update', $teacher);
        $data = $r->validate([
            'department_id' => 'sometimes|exists:departments,id',
            'office' => 'nullable|string|max:64',
            'academic_rank' => 'sometimes|string',
            'hired_on' => 'nullable|date',
        ]);
        $teacher->update($data);
        return new TeacherResource($teacher);
    }

    public function destroy(Teacher $teacher)
    {
        $this->authorize('delete', $teacher);
        $teacher->delete();
        return response()->noContent();
    }
}
