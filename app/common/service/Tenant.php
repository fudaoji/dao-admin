<?php
/**
 * Created by PhpStorm.
 * Script Name: Tenant.php
 * Create: 2022/9/23 18:32
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;

use app\common\model\Channel;
use app\common\model\TenantInfo;
use app\common\model\Tenant as TenantM;
use app\common\model\CpActivity as ActivityM;
use app\tenant\service\Auth as AuthService;

class Tenant extends Common
{
    /**
     * 获取管理的rid
     * @param array $tenant
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getRids($tenant = []){
        empty($tenant) && $tenant = request()->session()->get(SESSION_TENANT);
        $leader_id = self::getLeaderId($tenant);
        return self::isLeader($tenant) ? Channel::where('leader_id', $leader_id)
            ->column('rid') : Channel::alias('channel')
            ->where('channel.leader_id', $leader_id)
            ->where('channel.tenant_id', $tenant['id'])
            ->join('tenant tenant', 'tenant.id=channel.tenant_id')
            ->column('rid');
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
        return TenantM::where('pid|id', $tenant['id'])
            ->where('group_id','<>', AuthService::getChannelGroup('id'))
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
        if($tenant['pid'] == 0){
            return 1;
        }
        $leader_id = self::getLeaderId($tenant);
        if($leader_id == $tenant['pid']){
            return 2;
        }
        return 3;
    }

    /**
     * 获取leader id
     * @param mixed $tenant
     * @return \think\Model
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getLeaderId($tenant = []){
        empty($tenant) && $tenant = request()->session()->get(SESSION_TENANT);
        return $tenant['leader_id'] ?: $tenant['id'];
    }

    /**
     * 获取租户扩展信息
     * @param $params
     * @return \think\Model
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getInfo($params){
        if(! $info = TenantInfo::find($params['tenant_id'])){
            $info = new TenantInfo();
            $info->id = $params['tenant_id'];
            $info->save();
        }
        return $info;
    }

    /**
     * 获取leader id
     * @param $tenant
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function isLeader($tenant = []){
        empty($tenant) && $tenant = request()->session()->get(SESSION_TENANT);
        return $tenant['pid'] == 0;
    }

    /**
     * 今日发布量
     * @param array $tenant
     * @return int
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getTodayPublishNum($tenant = [])
    {
        empty($tenant) && $tenant = request()->session()->get(SESSION_TENANT);
        return ActivityM::where('tenant_id', $tenant['id'])
            ->whereDay('create_time')
            ->count();
    }
}