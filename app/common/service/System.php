<?php
/**
 * Created by PhpStorm.
 * Script Name: System.php
 * Create: 2023/2/20 7:55
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;


class System extends Common
{
    /**
     * 判断是否安装
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function isStalled(){
        return file_exists(base_path() . '/install.lock');
    }

}