<?php
/**
 * Created by PhpStorm.
 * Script Name: ListBuilder.php
 * Create: 9/21/22 11:09 PM
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\controller;


use app\admin\service\Auth;

class ListBuilder extends \app\common\controller\ListBuilder
{

    public function __construct()
    {
        parent::__construct();
        $this->setAuth(['super' => Auth::isSuperAdmin(), 'auth_list' => Auth::getAuthList()]);
    }
}