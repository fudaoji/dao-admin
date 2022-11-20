<?php
/**
 * Created by PhpStorm.
 * Script Name: ZsActivity.php
 * Create: 2022/9/27 10:21
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\model;


use app\BaseModel;

class CpActivity extends BaseModel
{
    protected $updateTime = false;

    /**
     * 京东联盟字段与数据库字段的映射
     */
    const FIELD_MAP = [
        "activityId" => 'id',
        "activityStatus" => 'status',
        "startTime" => "start_time",
        "endTime" => 'end_time',
        //"estimateFee" => "estimate_fee",
        "ygServiceFee" => "estimate_fee",
        "orderCntIn" => 'order_cnt_in',
        "serviceFee" => 'service_fee',
        "skuCnt" => 'sku_cnt',
        "skuToExamineCnt" => 'sku_examine_cnt',
        "skuTotalCnt" => 'sku_total_cnt',
        "title" => "title",
        "type" => "type",
        "unionId" => 'unionid',
        /*'qq' => 'qq',
        'dongdong' => 'dongdong',
        'jdGoodShop' => 'jd_good_shop'*/
    ];

    public static function statusList($id = null)
    {
        $list = [
            0 => '未发布',
            1 => '报名中',
            2 => '进行中',
            3 => '已删除',
            4 => '已终止',
            5 => '已结束'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }
}