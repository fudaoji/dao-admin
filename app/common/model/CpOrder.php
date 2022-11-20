<?php
/**
 * Created by PhpStorm.
 * Script Name: CpOrder.php
 * Create: 2022/10/19 15:14
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\model;


use app\BaseModel;

class CpOrder extends BaseModel
{
    protected $autoWriteTimestamp = false;

    /**
     * 京东联盟字段与数据库字段的映射
     */
    const FIELD_MAP = [
        'id' => 'id',
        "actualCommission" => 'actual_commission', //商品的实际佣金 float(0)
        "actualCosPrice" => 'actual_cos_price',//float(0) //实际计佣金额
        "actualFee" => 'actual_fee' ,//float(0) //推客分得的实际佣金 = actualCommission * finalRate
        //"estimateCommission" => 'estimate_commission', //float(0)  //预估佣金
        "commissionRate" => 'commission_rate', //float(20) //佣金比例(投放的广告主计划比例)
        "estimateCosPrice" => 'estimate_cos_price' ,//float(0) //预估计佣金额
        "estimateFee" => 'estimate_fee', //float(0) //推客的预估佣金 = estimateCosPrice * commissionRate * finalRate
        "finalRate" => 'final_rate',//float(90) //最终分佣比例 = 1 - 京东抽佣点数
        //"exchangeRateAndUnit" => 'exchangeRateAndUnit',//string(0) ""
        //"balanceExt": "{"20221020":0}" //
        //"channelId" => 'channel_id', //int(0) //渠道id
        "cpActId" => 'activity_id',//int(0) // 招商团活动id：当商品参加了招商团会有该值，为0时表示无活动
        "modifyTime" => 'modify_time',//string(0)  //
        "finishTime" => 'finish_time', //string(19) "2022-09-22 08:35:13" //订单完成时间
        "orderId" => 'order_id',// int(251550587169) //订单ID
        "orderTime" => 'order_time',// string(19) "2022-09-15 08:56:16" //下单时间
        "parentId" => 'parent_id',//int(0)  //父订单ID
        "payMonth" => 'pay_month', // int(20221020)
        "payPrice" => 'pay_price',// float(0)
        "pid" => 'pid',//string(0) //PID
        "plus" => 'plus', //int(2) //1 plus 2非plus
        "price" => 'price', // float(0) //商品单价
        //"proPriceAmount" => 'pro_price_amount', // float(0) 价保赔付金额
        //"sellingFlag" =>  '',//int(2)
        "siteId" => 'site_id',// int(0)
        "skuFrozenNum" => 'sku_frozen_num', //int(0) 售后数量
        "skuId" => 'sku_id', // int(10055992269185) //
        "skuImgUrl" => 'sku_img_url',// string(64) "jfs/t1/9328/5/18494/84975/62c68c86Ee66f6f5e/c44d60cc72001848.jpg"
        "skuName" => 'sku_name',// string(173) "仙莉丝花香洁厕灵洁厕液卫生间马桶清洁剂洁厕净厕所除臭去污强力除尿垢 享受7天内质量问题退换货【注意：从物流签收日开始算"
        "skuNum" => 'sku_num',// int(1)
        "skuReturnNum" => 'sku_return_num',// int(0) //商品已退货数量
        "skuShopName" => 'sku_shop_name',// string(18) "仙莉丝旗舰店"
        "positionId" => 'position_id',// int(3004063211) //推广位ID
        "rid" =>'rid',//rid
        "traceType" => 'trace_type', //int(2) //同跨店：2同店 3跨店
        "unionId" => 'union_id',//int(2022372433)
        "unionRole" => 'union_role',//int(1) //站长角色：1 推客 2 团长 3内容服务商
        "unionTag" => 'union_tag', //string(12) //"普通订单"
        "validCode" => 'valid_code', //string(9) //"已完成"
    ];

    /**
     * 状态列表
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function orderStatusList($id = null){
        $list = [
            15 => '待付款',
            16 => '已付款',
            17 => '已完成'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }
}