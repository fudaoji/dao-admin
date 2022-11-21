<?php
/**
 * Created by PhpStorm.
 * Script Name: Index.php
 * Create: 2022/11/2 20:18
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace App\home\controller;

class Index
{
    public function index(){
        return response("Hello, DaoAdmin");
    }
}