<?php
/**
 * Created by PhpStorm.
 * Script Name: Payment.php
 * Create: 2023/3/16 10:29
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\constant;


class Payment
{
    const WX_NATIVE = 'WX_NATIVE';
    const WX_JSAPI = 'WX_JSAPI';
    const NOT_PAY = 0;
    const PAID = 1;
    const FINISH = 2;
    const AFTER_SALE = 5;
    const REFUND = 7;

    static function orderStatus($id = null){
        $list = [
            self::NOT_PAY => '待支付',
            self::PAID => '已付款',
            self::REFUND => '已退款',
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }

    /**
     * 实物订单状态
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function entityOrderStatus($id = null){
        $list = [
            self::NOT_PAY => '待支付',
            self::PAID => '已付款',
            self::FINISH => '已完成',
            self::AFTER_SALE => '售后中',
            self::REFUND => '已退款',
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }

    public static function channels($id = null){
        $list = [
            self::WX_NATIVE => '微信网页支付',
            self::WX_JSAPI => '微信公众号、小程序支付',
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }
}