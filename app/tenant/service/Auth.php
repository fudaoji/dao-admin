<?php
/**
 * Created by PhpStorm.
 * Script Name: Auth.php
 * Create: 2022/9/20 14:56
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\service;

use app\common\model\TenantDepartment;
use app\common\model\TenantGroup;
use app\common\model\TenantRule;
use app\common\service\Tenant;
use support\Log;

class Auth
{
    /**
     * 部门
     * @param null $admin
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getDepartments($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        $where = [
            ['status','=', 1],
            ['company_id','=', Tenant::getCompanyId($admin)]
        ];
        return TenantDepartment::where($where)
            ->column('title', 'id');
    }

    /**
     * 能够看到的角色
     * @param null $admin
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getGroups($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        $where = [
            ['status','=', 1],
            ['company_id','=', Tenant::getCompanyId($admin)]
        ];
        return TenantGroup::instance()->where($where)
            ->column('title', 'id');
    }

    /**
     * 判断是否有权限
     * @param $admin
     * @param string $node
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function check($admin = null, $node = ''){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        if(self::isSuperAdmin($admin)){
            return true;
        }
        if(! $rule = TenantRule::where('href', 'like', '%'.$node.'%')->find()){
            return true;
        }
        $group = TenantGroup::find($admin['group_id']);
        $group_rules = explode('', $group['rules']);
        return in_array($rule['id'], $group_rules);
    }

    /**
     * 是否创始人
     * @param null $admin
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function isSuperAdmin($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        return $admin['company_id'] == 0;
    }

    /**
     * 获取菜单列表
     * @param $admin
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public static function getMenuList($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        $where = [
            ['status','=', 1],
            ['type', '=', TenantRule::TYPE_MENU]
        ];
        if(!self::isSuperAdmin($admin)) {
            $group = TenantGroup::where('company_id', Tenant::getCompanyId($admin))
                ->find($admin['group_id']);
            $where[] = ['id', 'in', explode(',', $group['rules'])];
        }
        $menus = TenantRule::field('id,pid,title,icon,href,target')
            ->where($where)
            ->order('sort', 'desc')
            ->select();
        $menus = self::buildMenuChild(0, $menus);
        return $menus;
    }

    /**
     * 递归获取子菜单
     * @param $pid
     * @param $menuList
     * @return array
     * @author: fudaoji<fdj@kuryun.cn>
     */
    private static function buildMenuChild($pid, $menuList){
        $treeList = [];
        foreach ($menuList as $v) {
            if ($pid == $v['pid']) {
                $node = $v;
                $child = self::buildMenuChild($v['id'], $menuList);
                if (!empty($child)) {
                    $node['child'] = $child;
                }
                $treeList[] = $node;
            }
        }
        return $treeList;
    }

    /**
     * 判断是否有权限
     * @param array $admin
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getAuthList($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        return self::isSuperAdmin($admin) ? [] : TenantGroup::getAuthList($admin);
    }

    /**
     * 判断是否有权限
     * @param array $admin
     * @param string $name
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkAuth($admin = null, $name = ''){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        if(self::isSuperAdmin($admin)) {
            return  true;
        }
        return in_array($name, self::getAuthList($admin));
    }

    /**
     * 获取账号角色的下级角色ID
     * @param null $admin
     * @return int|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getChildGroupId($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        try{
            $group = TenantGroup::where('pid', '=', $admin['group_id'])->find();
            return $group['id'];
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        return 0;
    }
}