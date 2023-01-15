<?php
/**
 * Created by PhpStorm.
 * Script Name: event.php
 * Create: 2023/1/15 16:25
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */


return [
    'user.login' => [
        [app\common\event\TenantEvent::class, 'login'],
    ],
];