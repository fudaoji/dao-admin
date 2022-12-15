<?php
/**
 * Created by PhpStorm.
 * Script Name: Auth.php
 * Create: 2022/11/12 9:35
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\api\service;

use app\common\model\TenantDepartment;
use Webman\Http\Request;

class Auth
{
    /**
     * 退出
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function logout(){
        \cache(self::getToken(), null);
    }

    /**
     * 租户信息
     * @param null $key
     * @return mixed|null
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function tenantInfo($key = null){
        $admin = \cache(self::getToken());
        return $key!==null ? $admin[$key] : $admin;
    }

    /**
     * 客户扩展信息
     * @param null $key
     * @return mixed|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function tenantExtendInfo($key = null){
        $admin = self::tenantInfo();
        $extend = TenantDepartment::find($admin['id']);
        return $extend ? ($key!==null ? $extend[$key] : $extend) : '';
    }
    /**
     * 获取token
     * @return array|string|null
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getToken(){
        return \request()->header('token');
    }

    /**
     * 登录
     * @param $user
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function login($user){
        \cache(self::getToken(), $user, 7 * 86400);
    }

    /**
     * check whether login
     * @param string $token
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function checkLogin(string $token){
        $user = \cache($token);
        return  ($user && !empty($user['id']));
    }

    /**
     * check whether login is required
     * @param Request $request
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function needLogin(Request $request){
        $node = strtolower($request->controller);
        $white = ['auth'];
        return ! in_array($node, $white);
    }

    /**
     * 验证token是否存在
     * @param string $token
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkToken(string $token){
        return \cache($token);
    }

    /**
     * check whether token is required
     * @param Request $request
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function needToken(Request $request){
        $node = strtolower($request->controller . '/' . $request->action);
        $white = ['auth/gettoken', 'auth/captcha'];
        return ! in_array($node, $white);
    }

    /**
     * 续期
     * @param string $token
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function refreshToken(string $token)
    {
        \cache($token, \cache($token), 7 * 86400);
    }
}