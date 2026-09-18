<?php

namespace App\Exceptions\Location;

use App\Exceptions\BusinessException;

class LocationUnavailableException extends BusinessException
{
    public function __construct(string $field = 'city_code')
    {
        parent::__construct(
            message: 'The selected location is unavailable',
            statusCode: 422,
            errors: [$field => ['The selected location is unavailable.']]
        );
    }
}
