<?php
/**
 * Created by PhpStorm.
 * Script Name: Setting.php
 * Create: 2022/9/20 10:16
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\model;

use app\BaseModel;
use ky\Faconfig;

class Setting extends BaseModel
{
    const CACHE_ALL = 'all';
    protected static $instance;

    public static function instance($data = [])
    {
        if (!self::$instance) {
            self::$instance = new self($data);
        }
        return self::$instance;
    }

    /**
     * 全局设置
     * @param bool $refresh
     * @return array
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function settings($refresh = false){
        if (! Faconfig::isInit() || $refresh) {
            $list = $this->cache(self::CACHE_ALL)->select();
            $data = [];
            foreach ($list as $v){
                $data[$v['name']] = json_decode($v['value'], true);
            }

            Faconfig::set(['system' => $data]);
        }

        return Faconfig::get('system');
    }
}