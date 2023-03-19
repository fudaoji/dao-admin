<?php
/**
 * Created by PhpStorm.
 * Script Name: Pay.php
 * Create: 2023/3/16 11:51
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\common\constant\Payment;
use app\common\model\OrderApp;
use app\TenantController;
use app\common\service\Qrcode as QrcodeService;
use app\common\service\Payment as PayService;

class Pay extends TenantController
{

    /**
     * @var OrderApp
     */
    private OrderApp $orderAppM;

    public function __construct()
    {
        parent::__construct();
        $this->orderAppM = new OrderApp();
    }

    /**
     * 应用采购付款页
     * @return mixed|\support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \Yansongda\Pay\Exception\ContainerException
     */
    public function payApp(){
        $order_no = input('order_id', 0);
        $order_info = $this->orderAppM->find($order_no);
        if (empty($order_info)){
            return $this->error('订单不存在');
        }

        if(request()->isPost()){ //轮询是否已支付
            if($order_info['order_status'] == Payment::PAID){
                return $this->success('支付成功！');
            }
            return $this->error('');
        }

        if($order_info['order_status'] == Payment::PAID){
            $this->error('订单已支付，请勿重复支付!', url('apps/index'));
        }

        $params = [
            'body'          => $order_info['body'],
            'order_no'  => $order_info['order_no'],
            'amount'     => (int)$order_info['amount']
        ];

        $config = ['notify_url'    => request()->domain() . url('onmessage/orderapp'),];
        $result = PayService::wxScan($params, $config);
        if(isset($result['data']['code_url'])) {
            //生成支付二维码
            $order_info['code_url'] = QrcodeService::generateCode($result['data']['code_url']);
        }else {
            return $this->error('发起支付出错，错误原因：' . $result['errmsg']);
        }
        $assign = [
            'order_info'  => $order_info
        ];
        return $this->show($assign);
    }
}