<?php
/**
 * Created by PhpStorm.
 * Script Name: CheckAuth.php
 * Create: 2022/9/19 19:15
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\middleware;

use app\tenant\service\Auth;
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
        $admin_info = request()->session()->get(SESSION_TENANT);

        if($this->whiteRoute($controller)){
            return $handler($request);
        }

        if (empty($admin_info)) {
            if($controller !== 'auth'){
                return redirect(url('auth/login'));
            }
        }else{
            if(! Auth::check($admin_info, '/' . $app . '/'. $controller . '/' . $action)){
                return error('暂无权限!');
            }
        }

        View::assign('app', $app);
        View::assign('controller', $controller);
        View::assign('action', $action);
        View::assign('tenant', $admin_info);
        return $handler($request);
    }

    /**
     * 免验控制器
     * @param string $controller
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    private function whiteRoute($controller = ''){
        $white = [
            'onmessage'
        ];
        if(in_array($controller, $white)){
            return true;
        }
        return false;
    }
}