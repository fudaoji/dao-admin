<?php
/**
 * Created by PhpStorm.
 * Script Name: JdCp.php
 * Create: 2022/10/21 9:18
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\crontab\traits;

use app\common\model\Tenant;
use app\common\model\CpGoods as GoodsM;
use app\common\model\CpOrder as OrderM;
use app\common\model\CpOrderSettle as SettleM;
use app\common\service\CpActivity;
use app\common\service\CpOrder;

trait JdCp
{
    /**
     * 删除90日前订单
     */
    private function delOldOrder()
    {
        ignore_user_abort();
        ob_start();
        ini_set("memory_limit", "-1");
        $now = time();
        $date = date("Y-m-d", $now - 86400 * 90);
        $num = OrderM::where('order_time', '<', $date)->delete();
        var_dump((new OrderM())->getLastSql());
        var_dump('删除条数：' . $num);
    }

    /**
     * 订单结算
     * Author: fudaoji<fdj@kuryun.cn>
     */
    private function orderSettle(){
        $end_time = date('Y-m-d H:i:s', strtotime(date('Y-m-01')) - 1);
        $end_time = '2022-10-31 23:59:59';
        $begin_time = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-01'))));
        $where = [
            ['valid_code', '=', 17],
            ['finish_time', 'between', [$begin_time, $end_time]]
        ];
        if($list = OrderM::where($where)
            ->field(['id','actual_fee as settle_fee'])
            ->select()
            ->toArray()){
            //var_dump((new OrderM)->getLastSql());
            SettleM::limit(100)
                ->insertAll($list);
        }
    }

    /**
     * 拉取退出申请
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function pullCancelGoods(){
        $leaders = $this->getLeaders();

        foreach ($leaders as $leader_id){
            $res = CpActivity::cancelGoodsList(['leader_id' => $leader_id, 'page_no' => 1, 'page_size' => 2]);
            if(!empty($res['result'])){
                $list = $res['result'];
                foreach ($list as $item){
                    if($goods = GoodsM::where('activity_id', $item['activityId'])
                        ->where('sku_id', $item['skuId'])
                        ->find()){
                        $goods->cancel_id = $item['id'];
                        $goods->cancel_status = $item['examineStatus'];
                        $goods->cancel_apply_time = $item['applyTime'];
                        $goods->cancel_reason = $item['cancelReason'];
                        $goods->save();
                    }
                }
            }
        }
    }

    /**
     * 拉取订单
     * Author: fudaoji<fdj@kuryun.cn>
     */
    private function pullOrder(){
        $leaders = $this->getLeaders();

        foreach ($leaders as $leader_id){
            var_dump($leader_id.':' . CpOrder::pullOrders(['leader_id' => $leader_id]));
        }
    }



    /**
     * 拉去团长活动
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    private function pullActivity(){
        $leaders = $this->getLeaders();

        foreach ($leaders as $leader_id){
            $acts = CpActivity::pull(['leader_id' => $leader_id]);
            if(!is_array($acts)){
                continue;
            }
            foreach ($acts as $act){
                $goods = CpActivity::pullGoodsList([
                    'leader_id' => $leader_id,
                    'activity_id' => $act['id'],
                    'tenant_id' => $act['tenant_id']
                ]);
                //var_dump($goods);
            }
            //var_dump($acts);
        }
    }

    private function getLeaders(){
        return Tenant::where([['status','=', 1], ['leader_id','=', 0]])
            ->column('id');
    }
}