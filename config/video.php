<?php

return [
    'disk' => env('VIDEO_STORAGE_DISK', 'local'),
    'playback_url_expiration_minutes' => (int) env('VIDEO_PLAYBACK_URL_EXPIRATION_MINUTES', 15),
    'upload_max_kilobytes' => (int) env('VIDEO_UPLOAD_MAX_KILOBYTES', 512000),
];
