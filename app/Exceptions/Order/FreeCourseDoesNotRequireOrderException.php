<?php

namespace App\Exceptions\Order;

use App\Exceptions\BusinessException;

class FreeCourseDoesNotRequireOrderException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Free courses do not require an order', 409);
    }
}
