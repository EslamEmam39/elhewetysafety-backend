<?php
namespace App\Exceptions\Enrollment;
use App\Exceptions\BusinessException;
class EnrollmentAccessDeniedException extends BusinessException { public function __construct() { parent::__construct('An active enrollment is required to access this course content.', 403); } }
