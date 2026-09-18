<?php

namespace App\Exceptions\Course;

use App\Exceptions\BusinessException;

class CourseReferenceUnavailableException extends BusinessException
{
    public function __construct(string $field)
    {
        parent::__construct('The selected course reference is unavailable', 422, [$field => ['The selected value is unavailable.']]);
    }
}
