<?php

return [
    // 定义中间表
    'relation' => [
        // 用户和角色中间表
        'user_role' => 'user_role_pivot',
        // 用户和权限中间表
        'user_permission' => 'user_permission_pivot',
        // 角色和权限中间表
        'role_permission' => 'role_permission_pivot'
    ],
    'cache' => [
        'enable' => true,
        'prefix' => 'betterde',
        'database' => 'cache'
    ]
];