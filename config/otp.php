<?php

return [
    'expiration_minutes' => (int) env('OTP_EXPIRATION_MINUTES', 10),
    'resend_seconds' => (int) env('OTP_RESEND_SECONDS', 60),
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),
];
