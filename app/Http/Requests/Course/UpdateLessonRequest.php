<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateLessonRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'code' => [
                'sometimes',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('lessons', 'code')->ignore($this->route('lesson')),
            ],
            'title' => ['sometimes', 'string', 'max:255'],
            'chapter_code' => ['sometimes', 'string', 'exists:chapters,code'],
            'sort_order' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'is_free_preview' => ['sometimes', 'boolean'],
        ];
    }
}
