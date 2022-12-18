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

use support\view\Raw;
use support\view\Twig;
use support\view\Blade;
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
    'options' => [
        'tpl_cache'          => getenv('APP_DEBUG') ? false : true,
        // 视图中使用的常量
        'tpl_replace_string'  =>  [
            '__STATIC__' => '/static',
            '__LIB__' => '/static/libs',
            '__CSS__' => '/static/css',
            '__JS__' => '/static/js',
            '__IMG__' => '/static/imgs'
        ],
        //'layout_on'     =>  true,
        //'layout_name'   =>  'default/layout/base',
    ]
];
