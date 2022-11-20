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

class CpGoods extends BaseModel
{
    /**
     * 京东联盟字段与数据库字段的映射
     */
    const FIELD_MAP = [
        "activityId" => 'activity_id',
        "adownerId" => 'ad_owner_id',
        "commissionRate" => "commission_rate", //佣金比例
        "couponAmount" => "coupon_amount",  //券额度
        "couponEndDate" => "coupon_end_date",
        "couponStartDate" => 'coupon_start_date',
        "discountPrice" =>  "discount_price",  //券后价
        "dongdong" => "dongdong",
        "endTime" => "end_time", //商品结束时间
        "imageUrl" => "image_url",
        "lowestPrice" => "lowest_price",  //最低价
        "nowCount" => "now_count",  //已发放
        "orderCntIn"=>"order_cnt_in",
        "orderGmvIn"=>"order_gmv_in",
        "price"=>"price",
        "pv"=>"pv" ,
        "sendNum"=>"send_num", //券总量
        "serviceFee"=>"service_fee",
        "serviceRate"=>"service_rate",  //服务比例
        "shopId"=>"shop_id" ,
        "shopName"=>"shop_name",
        "skuId"=>"sku_id" ,
        "skuName"=>"sku_name" ,
        "startTime"=>"start_time" ,
        "status"=>"status",
        'examineStatus' => 'status'
    ];

    /**
     * 商品审核状态
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function statusList($id = null){
        $list = [
            0 => '待审核',
            1 => '已通过',
            2 => '已拒绝',
            3 => '已终止',
            4 => '已过期',
            5 => '已停止',
            6 => '已取消'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }

    /**
     * 商品退出状态
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function cancelStatusList($id = null){
        $list = [
            0 => '待审核',
            1 => '已通过',
            2 => '已拒绝'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }
}