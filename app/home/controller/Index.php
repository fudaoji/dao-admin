<?php
/**
 * Created by PhpStorm.
 * Script Name: Index.php
 * Create: 2022/11/2 20:18
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\home\controller;

use app\BaseController;

class Index extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        return $this->show();
    }
}