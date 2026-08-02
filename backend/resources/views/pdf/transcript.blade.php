<!DOCTYPE html>


<html>
<head>
<meta charset="utf-8">
<title>Official Transcript — {{ $t['student']['name'] }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
    .header { display: table; width: 100%; border-bottom: 2px solid #1e293b; padding-bottom: 12px; margin-bottom: 16px; }
    .header .col { display: table-cell; vertical-align: top; }
    .header .col.center { text-align: center; }
    .header h1 { margin: 0; font-size: 20px; color: #0f172a; letter-spacing: 1px; }
    .header .subtitle { color: #64748b; font-size: 10px; }
    .meta { margin-bottom: 12px; }
    .meta table { width: 100%; border-collapse: collapse; }
    .meta td { padding: 3px 6px; }
    .meta .label { color: #64748b; width: 130px; }
    table.courses { width: 100%; border-collapse: collapse; margin-top: 6px; }
    table.courses th, table.courses td { border: 1px solid #cbd5e1; padding: 5px 7px; }
    table.courses th { background: #f1f5f9; text-align: left; font-size: 10px; }
    .sem-title { background: #0f172a; color: #fff; padding: 6px 10px; margin-top: 14px; font-weight: bold; }
    .summary { margin-top: 18px; padding: 12px; background: #f8fafc; border: 1px solid #cbd5e1; }
    .summary .grid { display: table; width: 100%; }
    .summary .grid .cell { display: table-cell; padding: 4px 8px; }
    .summary .grid .label { color: #64748b; font-size: 10px; }
    .summary .grid .value { font-size: 16px; font-weight: bold; color: #0f172a; }
    .footer { margin-top: 24px; display: table; width: 100%; }
    .footer .col { display: table-cell; vertical-align: middle; }
    .footer .verify { font-size: 9px; color: #475569; }
    .footer .code { font-family: monospace; font-size: 10px; }
</style>
</head>
<body>
    <div class="header">
        <div class="col" style="width: 60%;">
            <h1>{{ $t['university'] }}</h1>
            <div class="subtitle">OFFICIAL ACADEMIC TRANSCRIPT</div>
        </div>
        <div class="col center" style="width: 40%;">
            {!! $t['qr_data'] ? '<img src="' . $t['qr_data'] . '" width="90" height="90"/>' : '' !!}
        </div>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td class="label">Student</td><td><strong>{{ $t['student']['name'] }}</strong></td>
                <td class="label">Student #</td><td>{{ $t['student']['number'] }}</td>
            </tr>
            <tr>
                <td class="label">Faculty</td><td>{{ $t['student']['faculty'] }}</td>
                <td class="label">Department</td><td>{{ $t['student']['dept'] }}</td>
            </tr>
            <tr>
                <td class="label">Entry Year</td><td>{{ $t['student']['entry'] }}</td>
                <td class="label">Status</td><td>{{ ucfirst($t['student']['status']) }}</td>
            </tr>
        </table>
    </div>

    @foreach ($t['semesters'] as $sem)
        <div class="sem-title">{{ $sem['semester'] ?? 'Semester' }}</div>
        <table class="courses">
            <thead>
                <tr>
                    <th style="width: 12%;">Code</th>
                    <th>Title</th>
                    <th style="width: 8%;">Credit</th>
                    <th style="width: 10%;">Score</th>
                    <th style="width: 10%;">Letter</th>
                    <th style="width: 10%;">GPA</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($sem['courses'] as $c)
                <tr>
                    <td>{{ $c['code'] }}</td>
                    <td>{{ $c['title'] }}</td>
                    <td>{{ $c['credit'] }}</td>
                    <td>{{ number_format((float) $c['score'], 2) }}</td>
                    <td>{{ $c['letter'] }}</td>
                    <td>{{ number_format((float) $c['gpa'], 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="summary">
        <div class="grid">
            <div class="cell">
                <div class="label">Cumulative GPA</div>
                <div class="value">{{ number_format((float) $t['summary']['cumulative_gpa'], 2) }} / 4.00</div>
            </div>
            <div class="cell">
                <div class="label">Credits Earned</div>
                <div class="value">{{ $t['summary']['credits_earned'] }}</div>
            </div>
            <div class="cell">
                <div class="label">Credits Required</div>
                <div class="value">{{ $t['summary']['credits_required'] }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="col" style="width: 70%;">
            <div class="verify">
                <strong>Verify this transcript</strong>: {{ $t['verify_url'] }}<br>
                Generated at: {{ $t['generated_at'] }}<br>
                Verification is available online and by QR scan for one year from date of issue.
            </div>
        </div>
        <div class="col" style="width: 30%; text-align: right;">
            <div class="code">SEAL / SIGNATURE</div>
        </div>
    </div>
</body>
</html>
