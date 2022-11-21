<?php

$expire = getenv('CACHE_EXPIRE') ?: 3600;
$prefix = getenv('CACHE_PREFIX') ?: 'dao_';
$driver = getenv('CACHE_DRIVER') ?: 'file';
return [
    'default' => $driver,
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
            'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
            'port' => getenv('REDIS_PORT') ?: 6379,
            'prefix' => $prefix,
            'expire' => $expire,
        ],
        'memcache' => [
            'type'  => 'memcached',
            // 缓存前缀
            'prefix' => $prefix,
            'host'  => getenv('MEMCACHE_HOST') ?: '127.0.0.1',
            'port'  => getenv('MEMCACHE_PORT') ?: 11211
        ],
    ],
];