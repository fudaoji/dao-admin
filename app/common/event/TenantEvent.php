<?php
/**
 * Created by PhpStorm.
 * Script Name: TenantEvent.php
 * Create: 2023/1/15 16:24
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\event;


class TenantEvent
{
    public function login($data, $event){
        var_export($event);
        var_export($data);
    }
}