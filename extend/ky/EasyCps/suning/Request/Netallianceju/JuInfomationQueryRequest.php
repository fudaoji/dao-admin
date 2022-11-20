<?php
namespace ky\EasyCps\SuNing\Request\Netallianceju;

use ky\EasyCps\SuNing\SelectSuningRequest;
use ky\EasyCps\SuNing\RequestCheckUtil;

/**
 * 苏宁开放平台接口 - 批量查询大聚会商品信息
 *
 * @author suning
 * @date   2015-9-14
 */
class JuInfomationQueryRequest extends SelectSuningRequest
{


    public function getApiMethodName()
    {
        return 'suning.netalliance.juinfomation.query';
    }

    public function getApiParams()
    {
        return $this->apiParams;
    }

    public function check($pageNoMin = 1, $pageNoMax = 99999, $pageSizeMin = 10, $pageSizeMax = 50)
    {

    }

    public function getBizName()
    {
        return "queryJuInfomation";
    }

}

?>
