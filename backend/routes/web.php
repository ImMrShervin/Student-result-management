<?php

declare(strict_types=1);

use App\Models\Transcript;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'app' => config('app.name'),
    'status' => 'ok',
    'api' => url('/api/v1'),
    'docs' => url('/api/documentation'),
]));

Route::get('/verify/{code}', function (string $code) {
    $t = Transcript::where('verification_code', $code)->with('student.user')->firstOrFail();
    return view('transcripts.verify', ['t' => $t]);
})->name('transcripts.verify');
