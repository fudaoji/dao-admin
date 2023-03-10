<?php
/**
 * Created by PhpStorm.
 * Script Name: Auth.php
 * Create: 2022/7/20 17:04
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\api\controller\v1;

use app\ApiController;
use app\common\model\Tenant;
use think\facade\Validate;
use app\api\service\Auth as AuthService;

class Auth extends ApiController
{
    public function __construct()
    {
        parent::__construct(); // TODO: Change the autogenerated stub
    }

    /**
     * 登录
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login(){
        $post_data = $this->getAjax();
        Validate::extend('captcha', function ($value) {
            return $this->captchaCheck($value);
        });
        $validate = Validate::rule([
            //'code|验证码'    => 'captcha',
            'username'   => 'require|length:3,20',
            'password'  => 'require|length:6,20',
        ])->message([
            'code.require' => '请填写验证码',
            'code.captcha' => '验证码错误',
            'username.require'  => '请填写账号',
            'username.length'   => '账号错误',
            'password.require' => '请填写密码',
            'password.length'  => '密码错误'
        ]);

        $result = $validate->check($post_data);
        if (!$result) {
            return $this->jError($validate->getError());
        }

        $user = Tenant::where([['username', '=', $post_data['username']]])
            ->find();
        if ($user && $user['status'] == 1) {
            if(password_verify($post_data['password'], $user['password'])){
                $user['ip'] = request()->ip();
                $user['last_time'] = time();
                $user->save();
                unset($user['password']);
                AuthService::login($user);
                return $this->jSuccess('登录成功!', ['user' => $user]);
            }else{
                return $this->jError('账号或密码错误');
            }
        }else{
            return $this->jError('用户不存在或已被禁用');
        }
    }

    /**
     * 获取接口token
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function getToken(){
        $token = generate_token(32);
        \cache($token, true, 7 * 86400);
        return $this->jSuccess('success', ['token' => $token]);
    }
}