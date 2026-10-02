<?php

namespace App\Exceptions\Order;

use App\Exceptions\BusinessException;

class OrderCannotBeCancelledException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Only pending orders can be cancelled', 409);
    }
}
