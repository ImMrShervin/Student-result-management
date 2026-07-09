<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index(Request $r)
    {
        return response()->json(
            Semester::with('academicYear')
                ->orderByDesc('starts_on')
                ->paginate($r->integer('per_page', 20))
        );
    }

    public function current()
    {
        return response()->json(Semester::current()->with('academicYear')->first());
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:64',
            'term' => 'required|in:fall,spring,summer',
            'starts_on' => 'required|date',
            'ends_on' => 'required|date|after:starts_on',
            'enrollment_starts_on' => 'nullable|date',
            'enrollment_ends_on' => 'nullable|date',
            'is_current' => 'boolean',
        ]);
        return response()->json(Semester::create($data), 201);
    }

    public function years()
    {
        return response()->json(AcademicYear::orderByDesc('starts_on')->get());
    }
}
