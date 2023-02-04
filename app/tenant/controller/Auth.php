<?php
/**
 * Created by PhpStorm.
 * Script Name: Auth.php
 * Create: 2020/9/7 8:38
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace app\tenant\controller;
use app\TenantController;
use think\facade\Validate;
use app\common\model\Tenant;

class Auth extends TenantController
{
    /**
     * @var Tenant
     */
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Tenant();
    }

    /**
     * 注册
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\Exception
     */
    public function reg(){
        if (request()->isPost()) {
            $post_data = request()->only(['verify', 'username', 'repeat_password', 'password', '__token__']);

            Validate::extend('captcha', function ($value) {
                return $this->captchaCheck($value);
            });
            Validate::extend('token', function ($value) {
                return request()->checkToken('__token__');
            });

            $validate = Validate::rule([
                'verify|验证码'    => 'require|captcha',
                'username'   => 'require|length:3,20',
                'password'  => 'require|length:6,20',
                'repeat_password'  => 'confirm:password',
                '__token__' => 'require|token',
            ])->message([
                'verify.captcha' => '验证码错误',
                'username.require'  => '请填写账号',
                'username.length'   => '账号错误',
                'password.require' => '请填写密码',
                'password.length'  => '密码错误',
                'repeat_password'       => '两次密码不一致',
                '__token__.token' => '令牌失效',
            ]);
            $data = [
                'verify'   => $post_data['verify'],
                'username'   => $post_data['username'],
                'password'  => $post_data['password'],
                'repeat_password'  => $post_data['repeat_password'],
                '__token__' => $post_data['__token__'],
            ];
            $result = $validate->check($data);
            if (!$result) {
                return $this->error($validate->getError(), null, ['token' => token()]);
            }

            unset($data['__token__'], $data['repeat_password'], $data['verify']);
            $data['ip'] = request()->ip();
            $data['last_time'] = time();
            $data['password'] = fa_generate_pwd($data['password']);

            if($this->model->where('username', $data['username'])->count()){
                return $this->error('账号已存在!', null, ['token' => token()]);
            }
            if($user = $this->model->createOne($data)){
                $admin = $this->model->find($user['id']);
                session([SESSION_TENANT => $admin]);
                return $this->success('注册成功!', cookie('redirect_url') ? cookie('redirect_url') : url('index/index'));
            }

            return $this->error('注册失败，请联系客服！', '', ['token' => token()]);
        }

        if(session(SESSION_TENANT)){
            return $this->redirect(url('index/index'));
        }
        return $this->show();
    }

    /**
     * 登录
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\Exception
     */
    public function login(){
        if (request()->isPost()) {
            $post_data = request()->only(['verify', 'username', 'password', '__token__']);

            Validate::extend('captcha', function ($value) {
                return $this->captchaCheck($value);
            });
            Validate::extend('token', function ($value) {
                return request()->checkToken('__token__');
            });

            $validate = Validate::rule([
                'verify|验证码'    => 'require|captcha',
                'username'   => 'require|length:3,20',
                'password'  => 'require|length:6,20',
                '__token__' => 'require|token',
            ])->message([
                'verify.captcha' => '验证码错误',
                'username.require'  => '请填写账号',
                'username.length'   => '账号错误',
                'password.require' => '请填写密码',
                'password.length'  => '密码错误',
                '__token__.token' => '令牌失效',
            ]);
            $data = [
                'verify'   => $post_data['verify'],
                'username'   => $post_data['username'],
                'password'  => $post_data['password'],
                '__token__' => $post_data['__token__'],
            ];
            $result = $validate->check($data);
            if (!$result) {
                return $this->error($validate->getError(), null, ['token' => token()]);
            }

            $user = $this->model->where([['username', '=', $post_data['username']]])
                ->find();
            if ($user && $user['status'] == 1) {
                if(password_verify($post_data['password'], $user['password'])){
                    $user['ip'] = request()->ip();
                    $user['last_time'] = time();
                    $user->save();
                    unset($user['password']);
                    session([SESSION_TENANT => $user]);
                    if(!empty($post_data['keeplogin'])){
                        cookie('record_' . request()->app, $post_data['username']);
                    }
                    return $this->success('登录成功!', cookie('redirect_url') ? cookie('redirect_url') : url('index/index'));
                }else{
                    return $this->error('账号或密码错误', '', ['token' => token()]);
                }
            }else{
                return $this->error('用户不存在或已被禁用', '', ['token' => token()]);
            }
        }

        if(session(SESSION_TENANT)){
            return $this->redirect(url('index/index'));
        }
        return $this->show(['username' => cookie('record_' . request()->app)]);
    }
}