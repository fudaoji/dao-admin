<?php
/**
 * Created by PhpStorm.
 * Script Name: Interval.php
 * Create: 2023/1/15 16:45
 * Description: 自定义进程演示（定时器）
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace process;

use Workerman\Timer;

class Interval
{

    public function onWorkerStart()
    {
        // 每隔10秒任务
        Timer::add(10, function(){
            //var_dump("hello");
        });
    }
}