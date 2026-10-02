<?php
namespace App\Exceptions\Payment;
use App\Exceptions\BusinessException;
class OrderNotPayableException extends BusinessException
{
    public function __construct() { parent::__construct('Only pending orders can receive payment attempts', 409); }
}
