<?php
/**
 * Created by PhpStorm.
 * Script Name: Common.php
 * Create: 2022/12/7 9:11
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\constant;


class Common
{
    const YES = 1;
    const NO = 0;

    public static function status($id = null){
        $list = [
            self::YES => '启用',
            self::NO => '禁用'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }

    public static function yesOrNo($id = null){
        $list = [
            self::YES => '是',
            self::NO => '否'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }

    public static function goodsStatus($id = null){
        $list = [
            self::YES => '上架',
            self::NO => '下架'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }
}