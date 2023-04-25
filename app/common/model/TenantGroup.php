<?php
/**
 * Created by PhpStorm.
 * Script Name: AdminGroup.php
 * Create: 2022/9/20 14:40
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\model;


use app\BaseModel;

class TenantGroup extends BaseModel
{
    //protected $isCache = true;

    protected static $instance;

    public static function instance($data = [])
    {
        if (!self::$instance) {
            self::$instance = new self($data);
        }
        return self::$instance;
    }

    /**
     * 角色列表
     * @param null $admin
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function getGroups($admin = null){
        $where = [['status','=', 1]];
        return $this->where($where)
            ->column('title', 'id');
    }

    /**
     * 获取角色的权限
     * @param array $params
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getAuthList($params = []){
        $refresh = $params['refresh'] ?? false;
        $group = self::where('company_id', $params['company_id'])->find($params['group_id']);
        $where = [
            ['status','=', 1],
            ['type', '=', TenantRule::TYPE_AUTH],
            ['id', 'in', explode(',', $group['rules'])]
        ];

        $cache_key = self::instance()->getCacheKey('authlist' . $params['group_id']);
        $refresh && cache($cache_key, null);
        return TenantRule::where($where)
            ->cache($cache_key)
            ->column('href');
    }

    /**
     * 写入事件钩子
     * @param \think\Model $group
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function onAfterWrite($group){
        self::getAuthList([
            'company_id' => $group['company_id'],
            'group_id' => $group['id'],
            'refresh' => true
        ]);
    }
}