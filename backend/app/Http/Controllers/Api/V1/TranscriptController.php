<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Transcript;
use App\Services\TranscriptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TranscriptController extends Controller
{
    public function __construct(private readonly TranscriptService $service) {}

    public function generate(Request $r, Student $student)
    {
        $this->authorize('generateTranscript', $student);
        $transcript = $this->service->generate($student, $r->user()?->id);
        return response()->json([
            'id' => $transcript->id,
            'verification_code' => $transcript->verification_code,
            'download_url' => route('api.v1.transcripts.download', $transcript),
            'verify_url' => route('transcripts.verify', $transcript->verification_code),
        ]);
    }

    public function download(Transcript $transcript)
    {
        $this->authorize('view', $transcript);
        return Storage::disk('local')->download($transcript->pdf_path);
    }

    public function verify(string $code)
    {
        $t = Transcript::where('verification_code', $code)->with('student.user')->firstOrFail();
        return response()->json([
            'valid' => true,
            'issued_at' => $t->generated_at?->toIso8601String(),
            'student' => $t->student->user->full_name,
            'student_number' => $t->student->student_number,
            'cumulative_gpa' => $t->cumulative_gpa,
            'credits_earned' => $t->credits_earned,
        ]);
    }
}
