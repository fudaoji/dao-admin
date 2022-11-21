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
    '' => [
        app\common\middleware\CheckInstall::class,
        \Webman\Cors\CORS::class,
        app\common\middleware\AppInitialize::class,
        app\common\middleware\AppLang::class
    ],
    'install' => [
        \app\install\middleware\Check::class,
    ],
    'admin' => [
        app\common\middleware\FaconfigInitialize::class,
        \app\admin\middleware\CheckAuth::class,
    ],
    'tenant' => [
        app\common\middleware\FaconfigInitialize::class,
        \app\tenant\middleware\CheckAuth::class
    ],
    'api' => [
        app\common\middleware\FaconfigInitialize::class,
        \app\api\middleware\CheckAuth::class,
    ],
];