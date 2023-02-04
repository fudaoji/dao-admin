<?php
/**
 * Created by PhpStorm.
 * Script Name: CheckAuth.php
 * Create: 2022/9/19 19:15
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\home\middleware;

use support\View;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AssignData implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $handler): Response
    {
        $app        = request()->getApp();
        $controller = request()->getController();
        $action     = request()->getAction();
        $admin_info = request()->session()->get(SESSION_ADMIN);

        View::assign('app', $app);
        View::assign('controller', $controller);
        View::assign('action', $action);
        View::assign('user', $admin_info);
        View::assign('friend_link', []);
        return $handler($request);
    }
}