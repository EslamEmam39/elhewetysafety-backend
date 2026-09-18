<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateChapterRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'code' => [
                'sometimes',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('chapters', 'code')
                    ->ignore($this->route('chapter'))
            ],
            'title' => ['sometimes', 'string', 'max:255'],
            'sort_order' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::unique('chapters', 'sort_order')
                    ->where('course_id', $this->route('chapter')->course_id)
                    ->ignore($this->route('chapter'))
            ]
        ];
    }
}
