<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseCategoryRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return ['slug' => ['sometimes','string','max:255','alpha_dash',Rule::unique('course_categories','slug')->ignore($this->route('course_category'))], 'name_en' => ['sometimes','string','max:255'], 'name_ar' => ['sometimes','string','max:255'], 'is_active' => ['sometimes','boolean'], 'sort_order' => ['sometimes','integer','min:0']];
    }
}
