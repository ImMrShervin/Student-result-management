<?php

declare(strict_types=1);

namespace App\Http\Requests\Grade;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'attendance' => 'nullable|numeric|min:0|max:100',
            'assignment' => 'nullable|numeric|min:0|max:100',
            'quiz' => 'nullable|numeric|min:0|max:100',
            'project' => 'nullable|numeric|min:0|max:100',
            'midterm' => 'nullable|numeric|min:0|max:100',
            'practical' => 'nullable|numeric|min:0|max:100',
            'final_exam' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
