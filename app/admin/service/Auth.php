<?php
/**
 * Created by PhpStorm.
 * Script Name: Auth.php
 * Create: 2022/9/20 14:56
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\service;

use app\admin\model\AdminGroup;
use app\admin\model\AdminRule;

class Auth
{

    /**
     * 能够看到的角色
     * @param null $admin
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getGroups($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_ADMIN);
        $where = [['status','=', 1]];
        if(! self::isSuperAdmin()) {
            $where[] = ['id','>', 1];
        }
        return AdminGroup::instance()->where($where)
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
        empty($admin) && $admin = request()->session()->get(SESSION_ADMIN);
        if(self::isSuperAdmin($admin)){
            return true;
        }
        if(! $rule = AdminRule::where('href', 'like', '%'.$node.'%')->find()){
            return true;
        }
        $group = AdminGroup::find($admin['group_id']);
        $group_rules = explode('', $group['rules']);
        return in_array($rule['id'], $group_rules);
    }

    public static function isSuperAdmin($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_ADMIN);
        return $admin['group_id'] == 1;
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
        empty($admin) && $admin = request()->session()->get(SESSION_ADMIN);
        $where = [['status','=', 1], ['type', '=', AdminRule::TYPE_MENU]];
        if(!self::isSuperAdmin($admin)) {
            $group = AdminGroup::find($admin['group_id']);
            $where[] = ['id', 'in', explode(',', $group['rules'])];
        }
        $menus = AdminRule::field('id,pid,title,icon,href,target')
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
     */
    public static function getAuthList($admin = null){
        empty($admin) && $admin = request()->session()->get(SESSION_ADMIN);
        if(!self::isSuperAdmin($admin)){
            $where = [['status','=', 1], ['type', '=', AdminRule::TYPE_AUTH]];
            $group = AdminGroup::find($admin['group_id']);
            $where[] = ['id', 'in', explode(',', $group['rules'])];
            return  AdminRule::where($where)
                //->cache('authlist'.$admin['group_id'])
                ->column('href');
        }
        return [];
    }

    /**
     * 判断是否有权限
     * @param array $admin
     * @param string $name
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkAuth($admin = null, $name = ''){
        empty($admin) && $admin = request()->session()->get(SESSION_ADMIN);
        if(self::isSuperAdmin($admin)) {
            return  true;
        }
        return in_array($name, self::getAuthList($admin));
    }
}