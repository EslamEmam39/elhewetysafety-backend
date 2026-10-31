<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 24mm; }
        body { border: 3px solid #164e63; color: #0f172a; font-family: "DejaVu Sans", sans-serif; padding: 18mm; text-align: center; }
        h1 { color: #0e7490; font-size: 34px; margin: 0 0 20px; }
        .subtitle { font-size: 16px; margin: 12px 0; }
        .name { color: #164e63; font-size: 29px; font-weight: bold; margin: 18px 0; }
        .course { font-size: 20px; font-weight: bold; margin: 18px 0; }
        .arabic { direction: ltr; unicode-bidi: bidi-override; }
        .code { border-top: 1px solid #94a3b8; color: #475569; font-size: 11px; margin-top: 32px; padding-top: 12px; }
    </style>
</head>
<body>
    <h1>Certificate of Completion</h1>
    <p class="subtitle">This certifies that</p>
    <p class="name">{{ $recipientNameEn }}</p>
    <p class="subtitle arabic">{{ $recipientNameAr }}</p>
    <p class="subtitle">has been awarded a certificate for</p>
    <p class="course">{{ $courseTitle }}</p>
    <p class="code">Verification code: {{ $code }}</p>
</body>
</html>
