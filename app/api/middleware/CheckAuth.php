<?php
/**
 * Created by PhpStorm.
 * Script Name: CheckAuth.php
 * Create: 2022/9/19 19:15
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\api\middleware;

use ky\ErrorCode;
use support\View;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use app\api\service\Auth;

class CheckAuth implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $handler): Response
    {
        $request->app        = request()->getApp();
        $controller = explode('/', request()->getController());
        $request->controller = end($controller);
        $request->action     = request()->getAction();
        $header = \request()->header();
        if(($res = $this->checkSign()) !== true){
            return  json($res);
        }
        if(($res = $this->checkToken($request)) !== true){
            return  json($res);
        }
        if(($res = $this->checkLogin($request)) !== true){
            return  json($res);
        }

        //$request->data = $this->getAjax();   //中间件向路由传参
        return $handler($request);
    }

    /**
     * 校验请求token
     * @param $request
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: fudaoji<fdj@kuryun.cn>
     */
    private function checkToken($request){
        if(Auth::needToken($request)){
            $header = request()->header();
            if(empty($header['token'])){
                return ['code' => ErrorCode::TokenInvalid, 'msg' => dao_trans('token缺失')];
            }

            if(! Auth::checkToken($header['token'])){
                return ['code' => ErrorCode::TokenInvalid, 'msg' => dao_trans('token过期')];
            }
            Auth::refreshToken($header['token']);
        }
        return  true;
    }

    /**
     * 是否登录
     * @param $request
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function checkLogin($request){
        $header = request()->header();
        if(Auth::needLogin($request)){
            if(! Auth::checkLogin($header['token'])){
                return ['code' => ErrorCode::NotLogin, 'msg' => dao_trans('会话过期, 请先登录')];
            }
        }
        return  true;
    }

    /**
     * 签名验证
     * @author: fudaoji<fdj@kuryun.cn>
     */
    protected function checkSign(){
        if(in_array(strtolower(\request()->getAction()), ['captcha'])){
            return true;
        }
        $params = $this->getAjax();
        if(empty($params) || count($params) == 0){
            $params_str = '';
        }else{
            //签名步骤一：按字典序排序参数
            ksort($params);
            $params_str = "";
            foreach ($params as $k => $v)
            {
                if($k != "sign"){
                    $params_str .= ($k . "=" . json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . "&");
                }
            }
            $params_str = trim($params_str, "&");
        }
        //签名步骤二：在string后加入KEY
        $params_str .= config('app.app_key');
        //签名步骤三：MD5加密
        $sign = md5($params_str);
        //判断sign
        if($sign !== request()->header('sign')){
            return ['code' => ErrorCode::FailedCode, 'msg' => '非法请求', 'param' => $params_str];
        }
        return true;
    }

    protected function getAjax()
    {
        $json = \request()->rawBody();
        return json_decode($json, true);
    }
}