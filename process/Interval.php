<?php
/**
 * Created by PhpStorm.
 * Script Name: Interval.php
 * Create: 2023/1/15 16:45
 * Description: 自定义进程演示（定时器）
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace process;

use app\common\model\Timer as TimerM;
use app\common\service\System;
use Workerman\Timer;

class Interval
{

    public function onWorkerStart()
    {
        if(System::isStalled()) {
            $task_list = TimerM::where('status', 1)
                ->select();
            foreach ($task_list as $task){
                // 每隔10秒任务
                Timer::add($task['seconds'], function ()use($task) {
                    file_get_contents($task['url']);
                });
            }
        }
    }
}