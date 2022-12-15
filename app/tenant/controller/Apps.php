<?php
/**
 * Created by PhpStorm.
 * Script Name: Apps.php
 * Create: 2022/12/15 8:14
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\common\model\App;
use app\TenantController;

class Apps extends TenantController
{
    /**
     * @var App
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new App();
    }

    public function index(){
        return $this->show();
    }

}