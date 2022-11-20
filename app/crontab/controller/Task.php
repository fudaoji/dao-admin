<?php
/**
 * Created by PhpStorm.
 * Script Name: Task.php
 * Create: 2022/9/27 11:03
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\crontab\controller;

use app\crontab\traits\JdCp;

class Task
{
    use JdCp;

    public function __construct()
    {
        set_time_limit(0);
    }

    public function fourClock(){
        $this->delOldOrder();
    }

    public function oneHour(){
        $this->pullCancelGoods();
    }

    public function oneMinutes(){
        $this->pullOrder();
    }

    public function fiveMinutes(){
        $this->pullActivity();
    }

    public function perDate20(){
        $this->orderSettle();
    }
}