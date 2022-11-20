<?php
/**
 * Created by PhpStorm.
 * Script Name: AdminRule.php
 * Create: 2022/9/20 14:40
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\model;

use app\BaseModel;

class TenantRule extends BaseModel
{
    protected $isCache = true;

    const TYPE_MENU = 1;
    const TYPE_AUTH = 2;

    protected static $instance;

    public static function instance($data = [])
    {
        if (!self::$instance) {
            self::$instance = new self($data);
        }
        return self::$instance;
    }

    /**
     * 类型
     * @param null $type
     * @return array
     */
    public function types($type=null){
        $list = [
            1 => '菜单',
            2 => '权限'
        ];
        return isset($list[$type]) ? $list[$type] : $list;
    }
}