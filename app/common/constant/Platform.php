<?php
/**
 * Created by PhpStorm.
 * Script Name: Platform.php
 * Create: 2022/12/16 8:17
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\constant;


class Platform
{
    const MP = 'mp';
    const MINI = 'mini';
    const APP = 'app';
    const PC = 'pc';

    /**
     * 类型
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function types($id = null){
        $list = [
            self::MP => '微信公众号',
            self::MINI => '微信小程序'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }
}