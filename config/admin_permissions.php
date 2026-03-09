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
                'users.manage',
                'reports.view',
                'monitoring.view',
                'notifications.manage',
                'api.admin',
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
