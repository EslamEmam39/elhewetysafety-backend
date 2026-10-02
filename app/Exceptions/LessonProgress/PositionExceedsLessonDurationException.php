<?php

namespace App\Exceptions\LessonProgress;

use App\Exceptions\BusinessException;

class PositionExceedsLessonDurationException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('The playback position cannot exceed the lesson duration.', 422);
    }
}
