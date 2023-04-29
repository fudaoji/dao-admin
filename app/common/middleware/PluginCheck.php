<?php
namespace app\common\middleware;

use app\common\service\TenantApp;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class PluginCheck implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if(! TenantApp::checkAppOpenStatus($request->plugin)){
            return \response(dao_trans('应用未开通或已到期!'));
        }
        return $handler($request);
    }
}