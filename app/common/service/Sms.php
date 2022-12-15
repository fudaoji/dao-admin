<?php
/**
 * Created by PhpStorm.
 * Script Name: Sms.php
 * Create: 2022/12/13 14:18
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;


class Sms extends Common
{
    /**
     * 驱动
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function drivers($id = null){
        $list = [
            'qcloud' => '腾讯云',
            'zhutong' => '助通',
            'yunxin' => '云信使',
            'shiyuan' => '示远'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }
}