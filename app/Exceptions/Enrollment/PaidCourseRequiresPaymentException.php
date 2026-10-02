<?php
namespace App\Exceptions\Enrollment;
use App\Exceptions\BusinessException;
class PaidCourseRequiresPaymentException extends BusinessException { public function __construct() { parent::__construct('Paid courses require a verified payment before enrollment.', 409); } }
