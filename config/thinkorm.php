<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 服务器地址
            'hostname' => getenv('DATABASE_HOSTNAME') ?: '127.0.0.1',
            // 数据库名
            'database' => getenv('DATABASE_DATABASE') ?: 'fasass',
            // 数据库用户名
            'username' => getenv('DATABASE_USERNAME') ?: 'root',
            // 数据库密码
            'password' => getenv('DATABASE_PASSWORD') ?: '123456',
            // 数据库连接端口
            'hostport' => getenv('DATABASE_HOSTPORT') ?: '3306',
            // 数据库连接参数
            'params' => [],
            // 数据库编码默认采用utf8
            'charset' => getenv('DATABASE_CHARSET') ?: 'utf8mb4',
            // 数据库表前缀
            'prefix' => getenv('DATABASE_PREFIX') ?: 'fa_',
            // 断线重连
            'break_reconnect' => true,
            // 关闭SQL监听日志
            'trigger_sql' => false,
            // 开启字段缓存
            //'fields_cache'      => getenv('APP_DEBUG') ? false : true,
            'fields_cache'      => false,
            // 字段缓存路径
            'schema_cache_path' => runtime_path() . '/schema',
            // 开启自动写入时间戳字段
            'auto_timestamp' => true,
        ],
    ],
];