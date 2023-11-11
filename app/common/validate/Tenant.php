<?php
// +----------------------------------------------------------------------
// | [KyPHP System] Copyright (c) 2020 http://www.kuryun.com/
// +----------------------------------------------------------------------
// | [KyPHP] 并不是自由软件,你可免费使用,未经许可不能去掉KyPHP相关版权
// +----------------------------------------------------------------------
// | Author: fudaoji <fdj@kuryun.cn>
// +----------------------------------------------------------------------

/**
 * Created by PhpStorm.
 * Script Name: Vehicle.php
 * Create: 2020/5/23 下午4:22
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace app\common\validate;
use app\common\model\Tenant as TenantM;

class Tenant extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->rule = array_merge([
            'group_id' => 'require|integer',
            'username' => 'require|length:5,20',
            'password' => 'require|length:6,20',
            'oldpassword' => 'require|length:6,20',
            'email' => 'email',
            'mobile' => 'mobile',
            'realname' => 'length:2,30',
        ],
            $this->rule
        );
        $this->message = array_merge([
            'group_id.require' => 'group_id参数错误',
            'username' => '请填写5-20位的账号',
            'username.checkExists' => '账号已存在',
            'password' => '请填写6-20位长度的密码',
            'oldpassword' => '旧密码错误',
            'realname' => '名称非法',
            'email' => '邮箱非法',
            'mobile' => '手机号非法',
        ],
            $this->message
        );
    }

    /**
     * 验证账号是否被占用
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     * @throws \think\db\exception\DbException
     * @author: fudaoji<fdj@kuryun.cn>
     */
    protected function checkExists($value, $rule, $data){
        $where[] = ['username','=', $value];
        if(isset($data['id'])){
            $where[] = ['id', '<>', $data['id']];
        }
        $exists = TenantM::where($where)->count();
        return $exists ? '账号已被占用' : true;
    }

    /**
     * 修改
     * @return Follow
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function sceneEdit(){
        return $this->only(['email', 'mobile', 'realname'])
            ->append('username', 'checkExists');
    }

    /**
     * 修改
     * @return Follow
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function sceneUpdatePwd(){
        return $this->only(['oldpassword', 'password']);
    }
}