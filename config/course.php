<?php

return [
    'thumbnail_disk' => env('COURSE_THUMBNAIL_DISK', 'public'),
    'thumbnail_upload_max_kilobytes' => (int) env('COURSE_THUMBNAIL_UPLOAD_MAX_KILOBYTES', 5120),
];
