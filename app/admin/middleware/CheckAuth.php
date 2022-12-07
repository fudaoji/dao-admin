<?php
/**
 * Created by PhpStorm.
 * Script Name: CheckAuth.php
 * Create: 2022/9/19 19:15
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\middleware;

use app\admin\service\Auth;
use support\View;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class CheckAuth implements MiddlewareInterface
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

        if (empty($admin_info)) {
            if($controller !== 'auth'){
                return redirect(url('auth/login'));
            }

        }else{
            if(! Auth::check($admin_info, '/' . $app . '/'. $controller . '/' . $action)){
                $res = ['code' => 0, 'msg' => dao_trans('暂无权限')];
                return \request()->isAjax() ? json($res) :
                    \view(config('app.dispatch_error'), $res);
            }
        }

        View::assign('app', $app);
        View::assign('controller', $controller);
        View::assign('action', $action);
        View::assign('admin', $admin_info);
        return $handler($request);
    }
}