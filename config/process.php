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
    // File update detection and automatic reload
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitor_dir' => [
                app_path(),
                config_path(),
                base_path() . '/plugin',
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ],
            // Files with these suffixes will be monitored
            'monitor_extensions' => [
                'php', 'html', 'htm', 'env'
            ]
        ]
    ],

    'task_test' => [
        // 这里指定进程类
        'handler' => process\TaskTest::class,
        // 监听的协议 ip 及端口 （可选）
        //'listen'  => 'websocket://0.0.0.0:8888',
        // 进程数 （可选，默认1）
        //'count'   => 2,
        // 进程运行用户 （可选，默认当前用户）
        //'user'    => '',
        // 进程运行用户组 （可选，默认当前用户组）
        //'group'   => '',
        // 当前进程是否支持reload （可选，默认true）
        //'reloadable' => true,
        // 是否开启reusePort （可选，此选项需要php>=7.0，默认为true）
        //'reusePort'  => true,
        // transport (可选，当需要开启ssl时设置为ssl，默认为tcp)
        //'transport'  => 'tcp',
        // context （可选，当transport为是ssl时，需要传递证书路径）
        //'context'    => [],
        // 进程类构造函数参数，这里为 process\Pusher::class 类的构造函数参数 （可选）
        //'constructor' => [],
    ],
];
