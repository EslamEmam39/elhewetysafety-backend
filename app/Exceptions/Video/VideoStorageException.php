<?php

namespace App\Exceptions\Video;

use App\Exceptions\BusinessException;

class VideoStorageException extends BusinessException
{
    public function __construct(string $message = 'The video storage service is temporarily unavailable.')
    {
        parent::__construct($message, 503);
    }
}
