<?php
/**
 * Created by PhpStorm.
 * Script Name: MediaGroup.php
 * Create: 2023/5/15 8:15
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;

use app\common\service\Tenant as TenantService;
use app\common\model\MediaGroup as GroupM;

class MediaGroup
{

    /**
     * Get hash list [{id:title, ...}]
     * @param array $params
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getIdToTitle($params = []){
        if(empty($params)){
            $params[] =['company_id', '=', TenantService::getCompanyId()];
        }
        return GroupM::where($params)->column('title', 'id');
    }
}