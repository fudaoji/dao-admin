<?php
/**
 * Created by PhpStorm.
 * Script Name: TenantGroup.php
 * Create: 2023/4/25 7:29
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\service;


use app\common\service\Tenant as TenantService;
use app\common\model\TenantGroup as GroupM;

class TenantGroup
{

    /**
     * 判断角色title是否被占用
     * @param array $params
     * @param null $company_id
     * @return int
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function checkTitleExists($params = [], $company_id = null){
        is_null($company_id) && $company_id = TenantService::getCompanyId();
        $query = GroupM::where('company_id', $company_id)
            ->where('title', $params['title']);
        if(!empty($params['id'])){
            $query = $query->where('id', '<>', $params['id']);
        }
        return  $query->count();
    }
}