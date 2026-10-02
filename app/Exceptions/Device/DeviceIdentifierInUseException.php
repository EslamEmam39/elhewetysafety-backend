<?php

namespace App\Exceptions\Device;

use App\Exceptions\BusinessException;

class DeviceIdentifierInUseException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('This device identifier is already registered to another account.', 409);
    }
}
