<?php
/**
 * Created by PhpStorm.
 * Script Name: CpOrder.php
 * Create: 2022/9/27 11:06
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace app\common\service;

use app\common\service\Tenant as TenantService;
use ky\EasyCps\Factory;
use app\common\model\CpOrder as CpOrderM;
use app\common\model\TenantInfo as TenantInfoM;
use ky\EasyCps\JingDong\Request\JdUnionOpenOrderRowQueryRequest;

class CpOrder extends Common
{
    /**
     * 获取渠道的预估服务费
     * @param array $params
     * @return float
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getChannelEstimateFee($params = []){
        $begin_time = $params['begin_time'];
        $end_time = $params['end_time'];
        $tenant_info = $params['tenant_info'];

        $rids = TenantService::getRids($tenant_info);
        $where = [
            ['order.leader_id', '=', TenantService::getLeaderId($tenant_info)],
            ['order.valid_code', 'in', [16, 17]]
        ];
        if(TenantService::isLeader($tenant_info)){
            $where[] = ['order.rid', '>', 0];
        }else{
            $where[] = ['order.rid', 'in', $rids];
        }

        $where[] = ['order_time', 'between', [$begin_time, $end_time . ' 23:59:59']];

        return CpOrderM::alias('order')
            ->where($where)
            ->join('cp_activity activity','activity.id=order.activity_id', 'left')
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('channel','channel.rid = order.rid', 'left')
            ->join('tenant tenant','tenant.id = channel.tenant_id', 'left')
        ->sum('order.estimate_fee');
    }

    /**
     * 获取渠道的预估结算费
     * @param array $params
     * @return float
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getChannelActualFee($params = []){
        $begin_time = date('Ymd', strtotime($params['begin_time']));
        $end_time = date('Ymd', strtotime($params['end_time']));
        $tenant_info = $params['tenant_info'];

        $rids = TenantService::getRids($tenant_info);
        $where = [
            ['order.leader_id', '=', TenantService::getLeaderId($tenant_info)],
            ['order.actual_fee', '>', 0]
        ];
        if(TenantService::isLeader($tenant_info)){
            $where[] = ['order.rid', '>', 0];
        }else{
            $where[] = ['order.rid', 'in', $rids];
        }

        $where[] = ['pay_month', 'between', [$begin_time, $end_time]];
        return CpOrderM::alias('order')
            ->where($where)
            ->join('cp_activity activity','activity.id=order.activity_id', 'left')
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('channel','channel.rid = order.rid', 'left')
            ->join('tenant tenant','tenant.id = channel.tenant_id', 'left')
            ->sum('actual_fee');
    }

    /**
     * 拉取订单
     * @param array $params
     * @return array|int
     * @throws \Exception
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function pullOrders($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['app_key']) || empty($leader['app_secret']) || empty($leader['union_id'])){
            return ['code' => 0, 'errmsg' => '请先完善团长账号信息'];
        }
        $request = new JdUnionOpenOrderRowQueryRequest();
        $time = date('Y-m-d H:i:00');
        $request->setStartTime(date('Y-m-d H:i:00', strtotime('-1 minute', strtotime($time))));
        $request->setEndTime($time);
        $page_no = $params['page_no'] ?? 1;
        $page_size = $params['page_size'] ?? 20;
        $request->setType(3);
        $request->setFields('goodsInfo');
        $request->setPageSize($page_size);
        $client = Factory::jingdong([
            'app_key' => $leader['app_key'],
            'app_secret' => $leader['app_secret'],
        ]);
        $has_more = true;
        $count = 0;
        $fields = CpOrderM::FIELD_MAP;
        while ($has_more){
            $request->setPageIndex($page_no++);
            $res = $client->execute($request);
            if($res['code'] && !empty($res['data']['data'])){
                $has_more = $res['data']['hasMore'];
                $list = $res['data']['data'];
                $update = [];
                $insert = [];
                foreach ($list as $item){
                    if($item['unionRole'] != 2 || $item['estimateCosPrice'] <= 0.00) {
                        continue;
                    } //只接收团长的有效订单
                    $count++;
                    $_data = [
                        'sku_owner' => $item['goodsInfo']['owner'],
                        'sku_product_id' => $item['goodsInfo']['productId'] ?? $item['goodsInfo']['mainSkuId'],
                        'sku_img_url' => $item['goodsInfo']['imageUrl'],
                        'sku_shop_name' => $item['goodsInfo']['shopName'] ?? '',
                        'leader_id' => $params['leader_id'],
                        'settle_fee' => $item['actualFee']
                    ];

                    foreach ($item as $k => $v){
                        isset($fields[$k]) && $_data[$fields[$k]] = $v;
                    }
                    if($exist = CpOrderM::find($item['id'])){
                        $_data['id'] = $exist['id'];
                        if($exist['settle_fee'] > 0) unset($_data['settle_fee']); //不去改变已结算的金额
                        if($_data['actual_fee'] == 0 && $exist['settle_fee'] > 0){
                            $_data['is_refund'] = 1; //表示结算后退款的
                        }
                        (new CpOrderM())->update($_data);
                        //empty($update[$item['id']]) && $update[$item['id']] = $_data;
                    }else{
                        empty($insert[$item['id']]) && $insert[$item['id']] = $_data;
                    }
                }

                if(count($insert)){
                    $insert = array_values($insert);
                    CpOrderM::limit(100)
                        ->insertAll($insert);
                }
                /*if(count($update)){
                    $update = array_values($update);
                    (new CpOrderM())->saveAll($update);
                }*/
            }else{
                $has_more = false;
            }
        }
        return $count;
    }
}