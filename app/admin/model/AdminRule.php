<?php
/**
 * Created by PhpStorm.
 * Script Name: AdminRule.php
 * Create: 2022/9/20 14:40
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\model;

use app\BaseModel;

class AdminRule extends BaseModel
{
    const TYPE_MENU = 1;
    const TYPE_AUTH = 2;

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