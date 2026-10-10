<?php

namespace App\Exceptions\Course;

use App\Exceptions\BusinessException;

class LessonPositionUnavailableException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            'The lesson order is already used in the selected chapter',
            422,
            ['sort_order' => ['The sort order has already been taken in the selected chapter.']],
        );
    }
}
