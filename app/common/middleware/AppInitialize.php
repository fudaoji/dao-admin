<?php

namespace app\common\middleware;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AppInitialize implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        if (in_array($request->app, config('app.deny_app_list'))) {
            $res = ['code' => 0, 'msg' => dao_trans('应用不存在：' . $request->app)];
            return  view(config('app.dispatch_error'), $res);
        }
        return $handler($request);
    }
}