<?php
/**
 * Created by PhpStorm.
 * Script Name: TenantApp.php
 * Create: 2023/4/6 9:49
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;


use app\common\model\TenantApp as TenantAppM;

class TenantApp
{
    /**
     * 获取可用app
     * @param null $company_id
     * @param array $where
     * @return array|\think\Collection|\think\db\Query[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getActiveApps($company_id = null, $where = []){
        is_null($company_id) && $company_id = Tenant::getCompanyId();
        $map = [
            'app.status' => 1,
            'ta.company_id' => $company_id
        ];
        $query = TenantAppM::alias('ta')
            ->where($map)
            ->where('ta.deadline','>', time())
            ->join('app app', 'app.name=ta.app_name')
            ->field(['app.*', 'ta.deadline']);
        $where && $query = $query->where($where);
        return $query->select();
    }

    /**
     * 验证是否有该应用权限
     * @param string $app_name
     * @param null $company_id
     * @return int
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function checkAppOpenStatus($app_name = '', $company_id = null){
        is_null($company_id) && $company_id = Tenant::getCompanyId();
        $map = [
            'app.status' => 1,
            'ta.company_id' => $company_id,
            'app.name' => $app_name
        ];
        $query = TenantAppM::alias('ta')
            ->where($map)
            ->where('ta.deadline','>', time())
            ->join('app app', 'app.name=ta.app_name');
        return $query->count();
    }

    /**
     * 当前开通应用数量
     * @param null $company_id
     * @param array $where
     * @return int
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getActiveAppsNum($company_id = null, $where = []){
        is_null($company_id) && $company_id = Tenant::getCompanyId();
        $map = [
            'app.status' => 1,
            'ta.company_id' => $company_id
        ];
        $query = TenantAppM::alias('ta')
            ->where($map)
            ->where('ta.deadline','>', time())
            ->join('app app', 'app.name=ta.app_name');
        $where && $query = $query->where($where);
        return $query->count();
    }

    /**
     * 过期应用数量
     * @param null $company_id
     * @param array $where
     * @return int
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getDeadlineAppsNum($company_id = null, $where = []){
        is_null($company_id) && $company_id = Tenant::getCompanyId();
        $map = [
            'app.status' => 1,
            'ta.company_id' => $company_id
        ];
        $query = TenantAppM::alias('ta')
            ->where($map)
            ->where('ta.deadline','<=', time())
            ->join('app app', 'app.name=ta.app_name');
        $where && $query = $query->where($where);
        return $query->count();
    }
}