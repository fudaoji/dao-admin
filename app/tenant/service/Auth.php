<?php
/**
 * Created by PhpStorm.
 * Script Name: Auth.php
 * Create: 2022/9/20 14:56
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\service;

use app\common\model\Tenant;
use app\common\model\TenantGroup;
use app\common\model\TenantRule;
use support\Log;

class Auth
{

    /**
     * 能够看到的角色
     * @param null $admin
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getGroups($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        $where = [['status','=', 1]];
        if(! self::isSuperAdmin()) {
            $where[] = ['id','>', 1];
        }
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
        /*if(self::isSuperAdmin($admin)){
            return true;
        }*/
        if(! $rule = TenantRule::where('href', 'like', '%'.$node.'%')->find()){
            return true;
        }
        $group = TenantGroup::find($admin['group_id']);
        $group_rules = explode('', $group['rules']);
        return in_array($rule['id'], $group_rules);
    }

    public static function isSuperAdmin($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_TENANT);
        return $admin['pid'] == 0;
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
        $group = TenantGroup::find($admin['group_id']);
        $where = [
            ['status','=', 1],
            ['type', '=', TenantRule::TYPE_MENU],
            ['id', 'in', explode(',', $group['rules'])]
        ];
        /*if(!self::isSuperAdmin($admin)) {
            $group = TenantGroup::find($admin['group_id']);
            $where[] = ['id', 'in', explode(',', $group['rules'])];
        }*/
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
                // todo 后续此处加上用户的权限判断
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

        /*if(!self::isSuperAdmin($admin)){
            $group = TenantGroup::find($admin['group_id']);
            $where[] = ['id', 'in', explode(',', $group['rules'])];
        }*/

        return TenantGroup::getAuthList($admin);
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
        /*if(self::isSuperAdmin($admin)) {
            return  true;
        }*/
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

    /**
     * 获取渠道角色
     * @param bool $field
     * @return int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getChannelGroup($field = true){
        if($field === true){
            $group = TenantGroup::where('name', '=', 'channel')->find();
        }else{
            $group = TenantGroup::where('name', '=', 'channel')->value($field);
        }
        return $group;
    }
}