<?php

return [
    'roles' => [
        'super_admin' => [
            'label' => 'Super Admin',
            'permissions' => ['*'],
        ],
        'admin' => [
            'label' => 'Admin',
            'permissions' => [
                'dashboard.view',
                'content.manage',
                'reports.view',
                'notifications.manage',
            ],
        ],
        'editor' => [
            'label' => 'Editor',
            'permissions' => [
                'dashboard.view',
                'content.manage',
                'reports.view',
            ],
        ],
        'student' => [
            'label' => 'Student',
            'permissions' => [],
        ],
    ],
];
