<?php
/**
 * Created by PhpStorm.
 * Script Name: Tenant.php
 * Create: 2022/9/23 18:32
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;

use app\common\model\Tenant as TenantM;

class Tenant extends Common
{
    /**
     * 获取团队成员ids
     * @param string $field
     * @param string $key
     * @param array $tenant
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getTeamIds($field='id', string $key = '', $tenant = []){
        empty($tenant) && $tenant = request()->session()->get(SESSION_TENANT);
        return TenantM::where('company_id|id', $tenant['id'])
            ->column($field, $key);
    }

    /**
     * 获取招商层级 1团长 2一招 3二招
     * @param array $tenant
     * @return int
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getLevel($tenant = []){
        empty($tenant) && $tenant = request()->session()->get(SESSION_TENANT);
        if($tenant['company_id'] == 0){
            return 1;
        }
        $leader_id = self::getCompanyId($tenant);
        if($leader_id == $tenant['company_id']){
            return 2;
        }
        return 3;
    }

    /**
     * 获取公司 id
     * @param mixed $tenant
     * @return \think\Model
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getCompanyId($tenant = []){
        empty($tenant) && $tenant = request()->session()->get(SESSION_TENANT);
        return $tenant['company_id'] ?: $tenant['id'];
    }

    /**
     * 是否创始人
     * @param $tenant
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function isLeader($tenant = []){
        empty($tenant) && $tenant = request()->session()->get(SESSION_TENANT);
        return $tenant['company_id'] == 0;
    }
}