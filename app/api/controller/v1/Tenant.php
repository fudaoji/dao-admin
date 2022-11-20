<?php
/**
 * Created by PhpStorm.
 * Script Name: Tenant.php
 * Create: 2022/11/16 8:30
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\api\controller\v1;


use app\api\service\Auth as AuthService;
use app\ApiController;

class Tenant extends ApiController
{
    /**
     * @var \app\common\model\Tenant
     */
    private $tenantM;

    public function __construct()
    {
        parent::__construct();
        $this->tenantM = new \app\common\model\Tenant();
    }

    /**
     * 修改个人密码
     * @return mixed
     * Author: Doogie<fdj@kuryun.cn>
     * @throws \think\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function updatePwd(){
        $post_data = $this->getAjax();
        try {
            $this->validate($post_data, "Tenant.updatePwd");
        }catch (\Exception $e){
            return $this->jError($e->getMessage());
        }

        $tenant_info = $this->tenantM->find(AuthService::tenantInfo('id'));
        if(! password_verify($post_data['oldpassword'], $tenant_info['password'])){
            return $this->jError('旧密码错误');
        }
        $res = $this->tenantM->update([
            'id' => $tenant_info['id'],
            'password' => fa_generate_pwd($post_data['password'])
        ]);
        if($res){
            return $this->jSuccess('密码修改成功');
        }

        return $this->jError('密码修改失败，请联系客服');
    }

    /**
     * 退出
     * @return \support\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function logout(){
        AuthService::logout();
        return $this->jSuccess('安全退出');
    }
}