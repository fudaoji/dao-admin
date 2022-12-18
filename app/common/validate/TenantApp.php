<?php
/**
 * Created by PhpStorm.
 * Script Name: TenantApp.php
 * Create: 2020/5/23 下午4:22
 * Description: 应用相关验证
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace app\common\validate;
use app\common\model\Tenant as TenantM;
use app\common\model\TenantApp as TenantAppM;

class TenantApp extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->rule = array_merge([
            'id'    => 'checkId',
            'app_name' => 'require',
            'company_id' => 'require|checkCompanyId',
            'deadline' => 'require|date'
        ],
            $this->rule
        );
        $this->message = array_merge([
            'app_name.require' => '请选择应用',
            'company_id.require' => '请选择客户',
            'company_id.checkCompanyId' => '客户不存在',
            'deadline.require' => '请选择到期时间',
            'deadline.date' => '到期时间格式错误',
        ],
            $this->message
        );
    }

    /**
     * 验证UID是否存在
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     * @author: fudaoji<fdj@kuryun.cn>
     */
    protected function checkCompanyId($value, $rule, $data)
    {
        return TenantM::find((int)$value) ? true : false;
    }

    /**
     * 验证ID是否存在
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     * @author: fudaoji<fdj@kuryun.cn>
     */
    protected function checkId($value, $rule, $data)
    {
        return TenantAppM::find((int)$value) ? true : false;
    }


    public function sceneEdit()
    {
        return $this->only(['__token__','id', 'deadline']);
    }


    public function sceneAdd()
    {
        return $this->only(['__token__', 'app_name','company_id','deadline']);
    }
}