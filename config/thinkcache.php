<?php

$expire = getenv('CACHE_EXPIRE', 3600);
$prefix = getenv('CACHE_PREFIX', 'fa_');
return [
    'default' => getenv('CACHE_DRIVER', 'file'),
    'stores' => [
        'file' => [
            'type' => 'File',
            // 缓存保存目录
            'path' => runtime_path() . '/cache/',
            // 缓存前缀
            'prefix' => $prefix,
            // 缓存有效期 0表示永久缓存
            'expire' => $expire,
        ],
        'redis' => [
            'type' => 'redis',
            'host' => getenv('REDIS_HOST', '127.0.0.1'),
            'port' => getenv('REDIS_PORT', 6379),
            'prefix' => $prefix,
            'expire' => $expire,
        ],
    ],
];