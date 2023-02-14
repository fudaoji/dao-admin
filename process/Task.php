<?php

/**
 * Created by PhpStorm.
 * Script Name: Task.php
 * Create: 2023/2/14 10:15
 * Description: 定时任务
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace process;

use app\common\model\Crontab as TaskM;
use Support\Container;
use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
        $task_list = TaskM::where('status', 1)
            ->select();
        foreach ($task_list as $task){
            new Crontab($task['rule'], function () use($task) {
                file_get_contents($task['url']);
            });
        }
    }
}