<?php

$host = getenv('REDIS_HOST') ?: '127.0.0.1';
$port = getenv('REDIS_PORT') ?: 6379;

return [
    'default' => [
        'host' => "redis://$host:$port",
        'options' => [
            'auth' => null,       // 密码，字符串类型，可选参数
            'db' => 0,            // 数据库
            'prefix' => 'dao_queue',       // key 前缀
            'max_attempts'  => 5, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
        ]
    ],
];
