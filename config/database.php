<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [
    // 默认数据库
    'default' => 'mysql',

    // 各种数据库配置
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DATABASE_HOSTNAME') ?: '127.0.0.1',
            'port'        => getenv('DATABASE_HOSTPORT') ?: '3306',
            'database'    => getenv('DATABASE_DATABASE') ?: '',
            'username'    => getenv('DATABASE_USERNAME') ?: 'root',
            'password'    => getenv('DATABASE_PASSWORD') ?: '123456',
            'unix_socket' => '',
            'charset'     => getenv('DATABASE_CHARSET') ?: 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
            'prefix'      => getenv('DATABASE_PREFIX') ?: '',
            'strict'      => true,
            'engine'      => null,
            'options' => [
                \PDO::ATTR_TIMEOUT => 3
            ]
        ],
    ],
];
