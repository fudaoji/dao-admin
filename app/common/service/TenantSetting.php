<?php
/**
 * Created by PhpStorm.
 * Script Name: TenantSetting.php
 * Create: 2022/12/19 17:35
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;
use app\common\model\TenantSetting as TenantSettingM;

class TenantSetting
{
    static $settings = [];

    /**
     * 获取商家配置
     * @param int $company_id
     * @return mixed|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getSettings($company_id = 0){
        if(! $company_id){
            return null;
        }
        if(empty(self::$settings[$company_id])){
            $list = TenantSettingM::where('company_id', $company_id)->select();
            $data = [];
            foreach ($list as $v){
                $data[$v['name']] = json_decode($v['value'], true);
            }
            self::$settings[$company_id] = $data;
        }

        return self::$settings[$company_id];
    }
}