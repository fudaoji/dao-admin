<?php
/**
 * Created by PhpStorm.
 * Script Name: Jd.php
 * Create: 2022/10/21 11:14
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\constant;


class Jd
{
    /**
     * 订单状态
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function orderStatus($id = null){
        $list = [
            15 => '待付款',
            16 => '已付款',
            17 => '已完成',
            0 => '无效'
        ];
        return isset($list[$id]) ? $list[$id] : ($id === null ? $list : $list[0]);
    }

    /**
     * 第1位：红包，第2位：组合推广，第3位：拼购，第5位：有效首次购（0000000000011XXX表示有效首购，最终奖励活动结算金额会结合订单状态判断，以联盟后台对应活动效果数据报表https://union.jd.com/active为准）,
     * 第8位：复购订单，第9位：礼金，第10位：联盟礼金，第11位：推客礼金，第12位：京喜APP首购，第13位：京喜首购，第14位：京喜复购，第15位：京喜订单，第16位：京东极速版APP首购，第17位白条首购，第18位校园订单，第19位是0或1时，均代表普通订单，第20位：预售订单，第21位：学生订单，第22位：全球购订单 ，第23位：京喜拼拼首购订单，第24位：京喜拼拼复购订单
     * Author: fudaoji<fdj@kuryun.cn>
     * @param string $tag
     * @return string
     */
    public static function unionTags($tag = ''){
        $titles = [1 => '红包', 2=> '组合推广', 3 => '拼购', 5=>'有效首次购', 8 => '复购订单',
            9 => '礼金', 10 => '联盟礼金', 11 => '推客礼金', 12 => '京喜APP首购', 13 => '有效首次购',
            14 => '京喜复购', 15 => '京喜订单', 16 => '京东极速版APP首购', 17=>'白条首购', 18=>'校园订单',
            20 => '预售订单',21 => '学生订单', 22 => '全球购订单',23 => '京喜拼拼首购订单',24 => '京喜拼拼复购订单'
        ];

        $tag_arr = array_reverse(str_split($tag));
        $res = [];
        foreach ($tag_arr as $k => $v){
            if($v && isset($titles[$k + 1])){
                $res[] = $titles[$k + 1];
            }
        }
        !count($res) && $res[] = '普通订单';
        return implode(',', $res);
    }

    /**
     * 自营|POP
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function owners($id = null){
        $list = [
            'p' => 'POP',
            'g' => '自营'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }

    /**
     * 是否plus
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function plus($id = null){
        $list = [
            0 => '',
            1 => 'plus'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }
    /**
     * 同店|跨店
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function traceTypes($id = null){
        $list = [
            2 => '同店',
            3 => '跨店'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }
}