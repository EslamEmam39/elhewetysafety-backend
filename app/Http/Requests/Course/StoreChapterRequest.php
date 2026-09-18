<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreChapterRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('chapters', 'code')
            ],
            'title' => ['required', 'string', 'max:255'],
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('chapters', 'sort_order')->where('course_id', $this->route('course')->id)
            ]
        ];
    }
}
