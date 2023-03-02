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

use support\Request;

return [
    'debug' => getenv('APP_DEBUG') ? true : false,
    'app_key' => getenv('APP_KEY') ?? '123456',
    'version' => '1.0.1',
    'error_reporting' => E_ALL,
    'default_timezone' => 'Asia/Shanghai',
    'request_class' => Request::class,
    'public_path' => base_path() . DIRECTORY_SEPARATOR . 'public',
    'runtime_path' => base_path(false) . DIRECTORY_SEPARATOR . 'runtime',
    'controller_suffix' => '',
    'controller_reuse' => true,

    // 默认应用
    'default_app' => 'home',
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list' => ['common'],

    'cors_domain'      => ['*', '127.0.0.1'],

    'dispatch_error'   => app_path() . '/common/view/common/jump.html',
    'dispatch_success' => app_path() . '/common/view/common/jump.html',
    'exception_tpl'    => app_path() . '/common/view/error/500.html',
    '404_tpl'    => app_path() . '/common/view/error/404.html',
    'error_message'    => '页面错误！请稍后再试～',
];
