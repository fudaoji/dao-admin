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
use app\common\model\TenantDepartment as DepartmentM;
use app\common\model\TenantApp as TenantAppM;

class Tenant extends Common
{
    /**
     * 获取商户apps
     * @param array $tenant_info
     * @param array $where //额外条件
     * @return array|\think\Collection|\think\db\Query[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException Author: fudaoji<fdj@kuryun.cn>
     */
    static function getCompanyActiveApps($tenant_info = [], $where = []){
        $map = [
            'app.status' => 1,
            'ta.company_id' => self::getCompanyId($tenant_info)
        ];
        $query = TenantAppM::alias('ta')
            ->where($map)
            ->where('ta.deadline','>', time())
            ->join('app app', 'app.name=ta.app_name')
            ->order('ta.update_time', 'desc')
            ->field([
                'ta.*','app.logo','app.desc','app.name','app.title','app.tenant_url','app.tenant_url_type'
            ]);
        $where && $query = $query->where($where);
        return $query->select();
    }

    /**
     * 获取部门
     * @param array $tenant
     * @return int
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getDepartment($tenant = []){
        empty($tenant) && $tenant = request()->session()->get(SESSION_TENANT);
        if($tenant['company_id'] == 0){
            return '创始人';
        }
        if($department = DepartmentM::find($tenant['department_id'])){
            return $department['title'];
        }
        return '';
    }

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

    /**
     * 获取商户列表字典
     * @param null $where
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getCompanyDict($where = null)
    {
        if(is_null($where)){
            $where = [['status', '=', 1]];
        }
        return TenantM::where($where)->column('username', 'id');
    }

    /**
     * 通用tenant_id条件
     * @param string $alias
     * @param array $tenant_info
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function tenantIdWhere($alias = '', $tenant_info = []){
        $alias = $alias ? $alias.'.' : '';
        $tenant_info = empty($tenant_info) ? self::sessionTenantInfo() : $tenant_info;
        if(self::isLeader($tenant_info)){
            return [$alias . 'company_id', '=', self::getCompanyId($tenant_info)];
        }
        return [$alias . 'tenant_id', '=', $tenant_info['id']];
    }

    /**
     * 通用company_id条件
     * @param string $alias
     * @param array $tenant_info
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    static function companyIdWhere($alias = '', $tenant_info = []){
        $alias = $alias ? $alias.'.' : '';
        $tenant_info = empty($tenant_info) ? self::sessionTenantInfo() : $tenant_info;
        return [$alias . 'company_id', '=', self::getCompanyId($tenant_info)];
    }

    static function sessionTenantInfo(){
        return \request()->session()->get(SESSION_TENANT);
    }
}