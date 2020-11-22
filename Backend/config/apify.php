<?php

return [
    'logging' => false,
    'enable' => false,
    'api_token_field' => 'api_token',
    'users' => [
        [
            'token' => 'all',
            'permissions' => [
                'user' => ['read', 'create', 'update', 'delete'],
            ]
        ]
    ]
];
