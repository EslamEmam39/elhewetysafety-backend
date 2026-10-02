<?php
namespace App\Http\Requests\CourseReview;
use App\Http\Requests\ApiFormRequest;
class CourseReviewIndexRequest extends ApiFormRequest { public function rules(): array { return ['per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]; } }
