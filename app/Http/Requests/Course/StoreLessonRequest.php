<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('lessons', 'code')],
            'title' => ['required', 'string', 'max:255'],
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('lessons', 'sort_order')->where('chapter_id', $this->route('chapter')->id),
            ],
            'is_free_preview' => ['sometimes', 'boolean'],
        ];
    }
}
