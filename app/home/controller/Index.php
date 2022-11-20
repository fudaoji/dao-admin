<?php
/**
 * Created by PhpStorm.
 * Script Name: Index.php
 * Create: 2022/11/2 20:18
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace App\home\controller;

use app\coroutine\Client;

class Index
{
    public function index(){
        $res = Client::instance(['port' => 9502])->send(range(1, 900));
        return response($res);
    }

    public function testExec(){
        exec('ls -al', $arr, $res);
        var_dump($arr, $res);
    }
}