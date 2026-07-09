<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\StudentRepositoryContract;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentRepository implements StudentRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Student::query()
            ->with(['user', 'department', 'faculty'])
            ->when($filters['department_id'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
            ->when($filters['faculty_id'] ?? null, fn ($q, $v) => $q->where('faculty_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('academic_status', $v))
            ->when($filters['entry_year'] ?? null, fn ($q, $v) => $q->where('entry_year', $v))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('student_number', 'like', "%{$v}%")
                        ->orWhereHas('user', fn ($qu) => $qu
                            ->where('first_name', 'like', "%{$v}%")
                            ->orWhere('last_name', 'like', "%{$v}%")
                            ->orWhere('email', 'like', "%{$v}%"));
                });
            })
            ->when($filters['min_gpa'] ?? null, fn ($q, $v) => $q->where('cumulative_gpa', '>=', $v))
            ->orderBy($filters['sort'] ?? 'id', $filters['dir'] ?? 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findByNumber(string $number): ?Student
    {
        return Student::with(['user', 'department', 'faculty'])
            ->where('student_number', $number)
            ->first();
    }

    public function topStudents(int $limit = 10): Collection
    {
        return Student::with(['user', 'department'])
            ->orderByDesc('cumulative_gpa')
            ->limit($limit)
            ->get();
    }

    public function averageGpaByDepartment(): Collection
    {
        return DB::table('students')
            ->join('departments', 'students.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('ROUND(AVG(cumulative_gpa),2) as avg_gpa'), DB::raw('COUNT(*) as students'))
            ->groupBy('departments.name')
            ->orderByDesc('avg_gpa')
            ->get();
    }
}
