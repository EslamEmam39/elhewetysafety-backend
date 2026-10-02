<?php

namespace App\Http\Requests\LessonProgress;

use App\Http\Requests\ApiFormRequest;

class UpdateLessonProgressRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'last_position_seconds' => ['required', 'integer', 'min:0'],
            'is_completed' => ['sometimes', 'boolean'],
        ];
    }
}
