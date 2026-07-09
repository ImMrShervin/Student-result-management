<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Transcript Verification — {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #e2e8f0; margin: 0; }
        .container { max-width: 640px; margin: 8vh auto; padding: 32px; background: #1e293b; border-radius: 12px; box-shadow: 0 12px 40px rgba(0,0,0,.3); }
        .ok { color: #10b981; font-size: 14px; letter-spacing: 2px; }
        h1 { margin: 8px 0 24px; font-size: 28px; }
        dl { display: grid; grid-template-columns: 180px 1fr; gap: 8px 16px; font-size: 15px; }
        dt { color: #94a3b8; }
        dd { margin: 0; font-weight: 600; }
        .footer { margin-top: 32px; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="ok">✓ VALID TRANSCRIPT</div>
        <h1>{{ config('app.name') }}</h1>
        <dl>
            <dt>Student</dt><dd>{{ $t->student->user->full_name }}</dd>
            <dt>Student Number</dt><dd>{{ $t->student->student_number }}</dd>
            <dt>Cumulative GPA</dt><dd>{{ number_format((float) $t->cumulative_gpa, 2) }} / 4.00</dd>
            <dt>Credits Earned</dt><dd>{{ $t->credits_earned }}</dd>
            <dt>Issued at</dt><dd>{{ $t->generated_at?->format('Y-m-d H:i') }} UTC</dd>
            <dt>Verification Code</dt><dd style="font-family: monospace">{{ $t->verification_code }}</dd>
        </dl>
        <div class="footer">This document has been digitally verified against university records.</div>
    </div>
</body>
</html>
