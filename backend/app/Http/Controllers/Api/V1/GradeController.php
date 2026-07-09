<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTOs\GradeInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Grade\StoreGradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Services\GradeService;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct(private readonly GradeService $service) {}

    public function index(Request $r)
    {
        $this->authorize('viewAny', Grade::class);
        return GradeResource::collection(
            Grade::with(['enrollment.student.user', 'enrollment.courseSection.course'])
                ->when($r->section_id, fn ($q, $v) => $q->whereHas('enrollment', fn ($qe) => $qe->where('course_section_id', $v)))
                ->when($r->published !== null, fn ($q) => $q->where('is_published', filter_var($r->published, FILTER_VALIDATE_BOOL)))
                ->paginate($r->integer('per_page', 20))
        );
    }

    public function upsert(StoreGradeRequest $r, Enrollment $enrollment)
    {
        $this->authorize('grade', $enrollment);
        $grade = $this->service->upsert(
            $enrollment,
            GradeInput::fromArray($r->validated()),
            $r->user()->id,
        );
        return new GradeResource($grade);
    }

    public function publish(Grade $grade)
    {
        $this->authorize('publish', $grade);
        return new GradeResource($this->service->publish($grade));
    }
}
