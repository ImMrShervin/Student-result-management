<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Student;
use App\Models\Transcript;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

final class TranscriptService
{
    public function __construct(private readonly GpaCalculator $gpa) {}

    public function generate(Student $student, ?int $generatedBy = null): Transcript
    {
        $student->load([
            'user',
            'department.faculty',
            'faculty',
            'semesterGpas.semester.academicYear',
            'enrollments.grade',
            'enrollments.courseSection.course',
            'enrollments.semester',
        ]);

        $cum = $this->gpa->cumulativeGpa($student);

        $groupedBySemester = $student->enrollments
            ->filter(fn ($e) => $e->grade?->is_published)
            ->groupBy('semester_id')
            ->map(function ($rows) {
                $sem = $rows->first()->semester;
                return [
                    'semester' => $sem?->name,
                    'courses'  => $rows->map(fn ($e) => [
                        'code'   => $e->courseSection->course->code,
                        'title'  => $e->courseSection->course->title,
                        'credit' => $e->courseSection->course->theory_credit + $e->courseSection->course->practical_credit,
                        'score'  => $e->grade->total_score,
                        'letter' => $e->grade->letter_grade?->value,
                        'gpa'    => $e->grade->gpa_points,
                    ])->values(),
                ];
            })->values();

        $code = Str::upper(Str::random(20));
        $verifyUrl = config('app.url') . '/verify/' . $code;
        $qrSvg = QrCode::format('svg')->size(140)->generate($verifyUrl);
        $qrData = 'data:image/svg+xml;base64,' . base64_encode((string) $qrSvg);

        $payload = [
            'student' => [
                'name'    => $student->user->full_name,
                'number'  => $student->student_number,
                'faculty' => $student->faculty->name,
                'dept'    => $student->department->name,
                'entry'   => $student->entry_year,
                'status'  => $student->academic_status->value,
            ],
            'semesters' => $groupedBySemester,
            'summary' => [
                'cumulative_gpa' => $cum['cumulative_gpa'],
                'credits_earned' => $cum['credits_earned'],
                'credits_required' => $student->credits_required,
            ],
            'verify_url' => $verifyUrl,
            'qr_data' => $qrData,
            'generated_at' => now()->toIso8601String(),
            'university' => config('app.name') . ' — ' . env('UNIVERSITY_NAME', 'University'),
        ];

        $pdf = Pdf::loadView('pdf.transcript', ['t' => $payload])->setPaper('a4');
        $filename = "transcripts/{$student->student_number}-{$code}.pdf";
        Storage::disk('local')->put($filename, $pdf->output());

        return Transcript::create([
            'student_id' => $student->id,
            'verification_code' => $code,
            'pdf_path' => $filename,
            'cumulative_gpa' => $cum['cumulative_gpa'],
            'credits_earned' => $cum['credits_earned'],
            'payload' => $payload,
            'generated_at' => now(),
            'generated_by' => $generatedBy,
        ]);
    }
}
