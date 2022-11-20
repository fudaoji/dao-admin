<?php
/**
 * Created by PhpStorm.
 * Script Name: ListBuilder.php
 * Create: 9/21/22 11:09 PM
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\tenant\service\Auth;

class ListBuilder extends \app\common\controller\ListBuilder
{

    public function __construct()
    {
        parent::__construct();
        $this->setAuth(['super' => 0, 'auth_list' => Auth::getAuthList()]);
    }
}