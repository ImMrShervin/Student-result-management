<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $r)
    {
        return response()->json(
            Announcement::with('author')
                ->whereNotNull('published_at')
                ->orderByDesc('published_at')
                ->paginate($r->integer('per_page', 10))
        );
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title' => 'required|string|max:180',
            'body' => 'required|string',
            'audience' => 'required|in:all,students,teachers,department,faculty',
            'department_id' => 'nullable|exists:departments,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'publish_now' => 'boolean',
        ]);
        $a = Announcement::create([
            ...$data,
            'author_id' => $r->user()->id,
            'published_at' => ($data['publish_now'] ?? true) ? now() : null,
        ]);
        return response()->json($a, 201);
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return response()->noContent();
    }
}
