<?php

return [
    'connection' => env('SCHOOL_DATA_CONNECTION', 'external'),
    'cache_ttl_seconds' => (int) env('SCHOOL_DATA_CACHE_TTL', 300),

    'students' => [
        'table' => env('SCHOOL_STUDENTS_TABLE', 'students'),
        'active_column' => env('SCHOOL_STUDENTS_ACTIVE_COLUMN', 'is_active'),
        'class_column' => env('SCHOOL_STUDENTS_CLASS_COLUMN', 'class_name'),
    ],

    'attendance' => [
        'table' => env('SCHOOL_ATTENDANCE_TABLE', 'attendances'),
        'date_column' => env('SCHOOL_ATTENDANCE_DATE_COLUMN', 'attendance_date'),
        'status_column' => env('SCHOOL_ATTENDANCE_STATUS_COLUMN', 'status'),
        'student_id_column' => env('SCHOOL_ATTENDANCE_STUDENT_ID_COLUMN', 'student_id'),
        'class_column' => env('SCHOOL_ATTENDANCE_CLASS_COLUMN', 'class_name'),
        'present_values' => explode(',', env('SCHOOL_ATTENDANCE_PRESENT_VALUES', '1,true,present,hadir')),
    ],

    'gallery' => [
        'table' => env('SCHOOL_GALLERY_TABLE', 'activity_photos'),
        'title_column' => env('SCHOOL_GALLERY_TITLE_COLUMN', 'title'),
        'path_column' => env('SCHOOL_GALLERY_PATH_COLUMN', 'file_path'),
        'date_column' => env('SCHOOL_GALLERY_DATE_COLUMN', 'activity_date'),
        'event_column' => env('SCHOOL_GALLERY_EVENT_COLUMN', 'event_name'),
    ],

    'weekly_gallery' => [
        'table' => env('SCHOOL_WEEKLY_GALLERY_TABLE', env('SCHOOL_ATTENDANCE_TABLE', 'attendances')),
        'path_column' => env('SCHOOL_WEEKLY_GALLERY_PATH_COLUMN', 'selfie_path'),
        'date_column' => env('SCHOOL_WEEKLY_GALLERY_DATE_COLUMN', env('SCHOOL_ATTENDANCE_DATE_COLUMN', 'attendance_date')),
        'status_column' => env('SCHOOL_WEEKLY_GALLERY_STATUS_COLUMN', env('SCHOOL_ATTENDANCE_STATUS_COLUMN', 'status')),
        'title_column' => env('SCHOOL_WEEKLY_GALLERY_TITLE_COLUMN', ''),
        'name_column' => env('SCHOOL_WEEKLY_GALLERY_NAME_COLUMN', 'student_name'),
        'class_column' => env('SCHOOL_WEEKLY_GALLERY_CLASS_COLUMN', env('SCHOOL_ATTENDANCE_CLASS_COLUMN', 'class_name')),
        'only_present' => filter_var(env('SCHOOL_WEEKLY_GALLERY_ONLY_PRESENT', true), FILTER_VALIDATE_BOOL),
        'limit' => (int) env('SCHOOL_WEEKLY_GALLERY_LIMIT', 12),
    ],

    'class_rollup' => array_map('trim', explode(',', env('SCHOOL_CLASS_ROLLUP', 'PG,TKA,TKB,1,2,3,4,5,6'))),
];
