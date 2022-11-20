<?php
/**
 * Created by PhpStorm.
 * Script Name: AdminGroup.php
 * Create: 2022/9/20 14:40
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\model;


use app\BaseModel;

class AdminGroup extends BaseModel
{
    //protected $isCache = true;

    protected static $instance;

    public static function instance($data = [])
    {
        if (!self::$instance) {
            self::$instance = new self($data);
        }
        return self::$instance;
    }
}