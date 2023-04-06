<?php
/**
 * Created by PhpStorm.
 * Script Name: OrderApp.php
 * Create: 2023/3/15 17:01
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;

use app\common\constant\Payment;
use app\common\model\OrderApp as OrderM;
use app\common\service\App as AppService;
use app\common\service\Tenant as TenantService;
use app\common\service\TenantWallet as WalletService;

class OrderApp extends Common
{
    /**
     * 商户支付订单数量
     * @param null $company_id
     * @return int
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getCompanyOrderNum($company_id = null){
        is_null($company_id) && $company_id = TenantService::getCompanyId();
        return OrderM::where('company_id', $company_id)
            ->where('order_status', Payment::PAID)
            ->count();
    }

    /**
     * 退款
     * @param array $order
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function afterRefund($order = []){
        $company_id = $order['company_id'];
        //1.update order
        OrderM::update([
            'id' => $order['id'],
            'refund_time' => time(),
            'refund_id' => $order['refund_id'],
            'order_status' => Payment::REFUND
        ]);

        //2. update wallet
        if($order['wallet']){
            WalletService::refundWallet([
                'company_id' => $company_id,
                'money' => $order['wallet'],
                'desc' => "订单退款，返回钱包支付部分"
            ]);
        }

        //3.deal app
        AppService::afterRefundApp([
            'app' => $order['app_name'],
            'company_id' => $company_id,
            'order' => $order
        ]);
    }

    /**
     * 支付成功后回调
     * @param array $params
     * @return array|mixed|\think\db\Query|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function payCallBack($params = []){
        $order_no = $params['out_trade_no'];
        $transaction_id = $params['transaction_id'];
        if($order = OrderM::where('order_no', $order_no)->find()){
            $company_id = $order['company_id'];
            //1.update order
            OrderM::update([
                'id' => $order['id'],
                'pay_time' => $params['pay_time'],
                'transaction_id' => $transaction_id,
                'order_status' => Payment::PAID
            ]);

            //2. update wallet
            if($order['wallet']){
                WalletService::releaseFrozen([
                    'company_id' => $company_id,
                    'money' => $order['wallet'],
                    'desc' => $order['body']
                ]);
            }

            //3.deal app
            AppService::afterBuyApp([
                'app' => $order['app_name'],
                'company_id' => $company_id,
                'order' => $order
            ]);
        }
        return $order;
    }

    /**
     * 下单
     * @param array $params
     * @return array
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function addOrder(array $params = [])
    {
        //1 insert order_app
        $app = $params['app'];
        $month = $params['month'];
        $is_wallet = $params['is_wallet'];
        $tenant = $params['tenant'];
        $company_id = TenantService::getCompanyId($tenant);

        $total = dao_money_format($app['price'] * $month);
        $amount = $total;
        $wallet = 0;
        if($total < 0.01){
            $amount = 0;
        }else if($is_wallet && $user_wallet = WalletService::getWallet($company_id, 'money')){
            if($user_wallet >= $total){
                $amount = 0;
                $wallet = $total;
            }else{
                $amount = $total - $user_wallet['money'];
                $wallet = $user_wallet;
            }
        }
        $total = $total * 100;
        $amount = $amount * 100;
        $desc = '采购应用' . $app['title'] . $month . '个月';
        $insert_order = [
            'app_name' => $app['name'],
            'company_id' => $company_id,
            'tenant_id' => $tenant['id'],
            'total' => $total,
            'amount' => $amount,
            'wallet' => $wallet,
            'body' => $desc,
            'order_no' => build_order_no(),
            'channel' => Payment::WX_NATIVE,
            'client_ip' => get_client_ip(),
            'month' => $month,
            'create_time' => time()
        ];
        if($amount <= 0){
            $insert_order['order_status'] = Payment::PAID;
            $insert_order['pay_time'] = time();
        }
        $insert_order['id'] = OrderM::insertGetId($insert_order);
        //frozen wallet
        if($wallet){
            if($amount > 0){
                WalletService::frozenMoney($company_id, $wallet);
            }else{
                WalletService::saveChange([
                    'company_id' => $company_id,
                    'money' => -$wallet,
                    'desc' => $insert_order['body']
                ]);
            }
        }
        return $insert_order;
    }
}