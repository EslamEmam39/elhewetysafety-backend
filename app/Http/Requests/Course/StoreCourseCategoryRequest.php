<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreCourseCategoryRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return ['slug' => ['required','string','max:255','alpha_dash',Rule::unique('course_categories','slug')], 'name_en' => ['required','string','max:255'], 'name_ar' => ['required','string','max:255'], 'is_active' => ['sometimes','boolean'], 'sort_order' => ['sometimes','integer','min:0']];
    }
}
