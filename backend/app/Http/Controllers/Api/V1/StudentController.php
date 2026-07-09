<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\StudentRepositoryContract;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function __construct(private readonly StudentRepositoryContract $repo) {}

    public function index(Request $r)
    {
        $this->authorize('viewAny', Student::class);
        return StudentResource::collection(
            $this->repo->paginate($r->all(), $r->integer('per_page', 15))
        );
    }

    public function store(Request $r)
    {
        $this->authorize('create', Student::class);
        $data = $r->validate([
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'nullable|string|max:32',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'faculty_id' => 'required|exists:faculties,id',
            'student_number' => 'required|string|unique:students,student_number',
            'entry_year' => 'required|integer|min:2000|max:2100',
            'current_semester' => 'nullable|integer|min:1|max:12',
            'credits_required' => 'nullable|integer|min:100|max:220',
        ]);

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'address' => $data['address'] ?? null,
                'code' => $data['student_number'],
                'is_active' => true,
            ]);
            $user->assignRole('student');

            $student = Student::create([
                'user_id' => $user->id,
                'department_id' => $data['department_id'],
                'faculty_id' => $data['faculty_id'],
                'student_number' => $data['student_number'],
                'entry_year' => $data['entry_year'],
                'current_semester' => $data['current_semester'] ?? 1,
                'credits_required' => $data['credits_required'] ?? 140,
            ]);
            return new StudentResource($student->load(['user', 'department', 'faculty']));
        });
    }

    public function show(Student $student)
    {
        $this->authorize('view', $student);
        return new StudentResource($student->load(['user', 'department.faculty', 'faculty', 'semesterGpas.semester']));
    }

    public function update(Request $r, Student $student)
    {
        $this->authorize('update', $student);
        $data = $r->validate([
            'department_id' => 'sometimes|exists:departments,id',
            'faculty_id' => 'sometimes|exists:faculties,id',
            'current_semester' => 'sometimes|integer|min:1|max:12',
            'credits_required' => 'sometimes|integer|min:100|max:220',
            'academic_status' => 'sometimes|string',
        ]);
        $student->update($data);
        return new StudentResource($student);
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);
        $student->delete();
        return response()->noContent();
    }
}
