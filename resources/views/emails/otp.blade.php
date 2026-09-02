<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $purpose === \App\Models\OtpCode::PURPOSE_EMAIL_VERIFICATION ? 'Verify your email' : 'Reset your password' }}</title>
</head>
<body>
    <h1>{{ $purpose === \App\Models\OtpCode::PURPOSE_EMAIL_VERIFICATION ? 'Verify your email' : 'Reset your password' }}</h1>
    <p>Your verification code is:</p>
    <p style="font-size: 28px; font-weight: bold; letter-spacing: 6px;">{{ $code }}</p>
    <p>This code expires in {{ $expirationMinutes }} minutes.</p>
    <p>If you did not request this code, you can ignore this email.</p>
</body>
</html>
