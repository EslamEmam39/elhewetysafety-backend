<?php

namespace App\Exceptions\Video;

use App\Exceptions\BusinessException;

class VideoNotReadyException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('This lesson video is not ready for playback.', 409);
    }
}
