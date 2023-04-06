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