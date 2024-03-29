<?php
namespace app\common\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AppLang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $lang = session('lang', DAO_LANG_CN);
        locale($lang); //设置全局语言环境
        session(['lang' => $lang]);
        return $handler($request);
    }
}