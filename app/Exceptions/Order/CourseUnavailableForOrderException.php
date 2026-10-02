<?php

namespace App\Exceptions\Order;

use App\Exceptions\BusinessException;

class CourseUnavailableForOrderException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('The selected course is unavailable for purchase', 422, [
            'course_slug' => ['The selected course is unavailable for purchase.'],
        ]);
    }
}
