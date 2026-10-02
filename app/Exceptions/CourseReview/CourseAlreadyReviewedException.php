<?php

namespace App\Exceptions\CourseReview;

use App\Exceptions\BusinessException;

class CourseAlreadyReviewedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('You have already reviewed this course.', 409);
    }
}
