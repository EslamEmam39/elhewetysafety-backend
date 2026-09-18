<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateLessonRequest extends ApiFormRequest
{
    public function rules(): array { return ['code' => ['sometimes','string','max:255','alpha_dash',Rule::unique('lessons','code')->ignore($this->route('lesson'))], 'title' => ['sometimes','string','max:255'], 'sort_order' => ['sometimes','integer','min:1',Rule::unique('lessons','sort_order')->where('chapter_id', $this->route('lesson')->chapter_id)->ignore($this->route('lesson'))], 'video_s3_key' => ['sometimes','nullable','string','max:2048'], 'video_hls_manifest_key' => ['sometimes','nullable','string','max:2048'], 'video_status' => ['sometimes',Rule::in(['pending','processing','ready','failed'])], 'duration_seconds' => ['sometimes','nullable','integer','min:0'], 'is_free_preview' => ['sometimes','boolean']]; }
}
