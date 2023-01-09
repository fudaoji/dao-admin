<?php
/**
 * Created by PhpStorm.
 * Script Name: FaconfigInitialize.php
 * Create: 2022/9/20 10:07
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\middleware;

use app\common\model\Setting;
use think\facade\Cache;
use think\facade\Db;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class FaconfigInitialize implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        /**
         * 要设置think-orm缓存功能需要设置一个满足PSR-16规范接口的缓存对象 :https://www.kancloud.cn/manual/think-orm/1258071
         * 修改了think-orm源码： https://github.com/top-think/think-orm/issues/372
         */
        Db::setCache(Cache::store());
        //Setting::instance()->settings();
        return $handler($request);
    }
}