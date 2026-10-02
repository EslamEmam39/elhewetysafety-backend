<?php
namespace App\Http\Requests\Enrollment;
use App\Http\Requests\ApiFormRequest;
class EnrollmentIndexRequest extends ApiFormRequest { public function rules(): array { return ['per_page'=>['sometimes','integer','min:1','max:100']]; } }
