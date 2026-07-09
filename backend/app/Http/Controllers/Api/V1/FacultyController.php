<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $r)
    {
        return FacultyResource::collection(
            Faculty::withCount('departments')
                ->when($r->q, fn ($q, $v) => $q->where('name', 'like', "%$v%"))
                ->orderBy('name')
                ->paginate($r->integer('per_page', 15))
        );
    }

    public function store(Request $r)
    {
        $this->authorize('create', Faculty::class);
        $data = $r->validate([
            'name' => 'required|string|max:120',
            'code' => 'required|string|max:16|unique:faculties,code',
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:users,id',
        ]);
        return new FacultyResource(Faculty::create($data));
    }

    public function show(Faculty $faculty)
    {
        return new FacultyResource($faculty->loadCount('departments'));
    }

    public function update(Request $r, Faculty $faculty)
    {
        $this->authorize('update', $faculty);
        $data = $r->validate([
            'name' => 'sometimes|string|max:120',
            'code' => 'sometimes|string|max:16|unique:faculties,code,' . $faculty->id,
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:users,id',
        ]);
        $faculty->update($data);
        return new FacultyResource($faculty);
    }

    public function destroy(Faculty $faculty)
    {
        $this->authorize('delete', $faculty);
        $faculty->delete();
        return response()->noContent();
    }
}
