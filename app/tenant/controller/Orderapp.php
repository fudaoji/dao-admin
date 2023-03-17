<?php
/**
 * Created by PhpStorm.
 * Script Name: Orderapp.php
 * Create: 2023/3/15 15:32
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;


use app\common\constant\Payment as PaymentConst;
use app\common\model\OrderApp as OrderM;
use app\common\service\OrderApp as OrderService;
use app\common\service\Tenant as TenantService;
use app\TenantController;
use support\Request;
use app\common\service\Payment as PayService;

class Orderapp extends  TenantController
{
    /**
     * @var OrderM
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new OrderM();
    }

    /**
     *
     * @param Request $request
     * @return mixed|\support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function index(Request $request){
        if($request->isPost()){
            $post_data = input('post.');
            $where = [
                ['order.company_id','=',  TenantService::getCompanyId()]
            ];
            $query = $this->model->alias('order')
                ->join('app app', 'app.name=order.app_name')
                ->where($where);
            $total = $query->count();
            if($total){
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order('order.id', 'desc')
                    ->field(['order.*', 'app.title'])
                    ->select();
                foreach ($list as $k => $v){
                    $v['total'] = dao_money_format($v['total']/100);
                    $v['amount'] = dao_money_format($v['amount']/100);
                    $v['order_status'] = PaymentConst::orderStatus($v['order_status']);
                    $v['pay_time'] = $v['pay_time'] ? date('Y-m-d H:i:s', $v['pay_time']) : '--';
                    $v['month'] .= '个月';
                    $list[$k] = $v;
                }
            }else{
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        //['订单号','应用','会员','总金额','实付金额', '下单时间', '支付状态','支付时间']
        $builder = new ListBuilder();
        $builder->addTableColumn(['title' => '订单号', 'field' => 'order_no', 'minWidth' => 150])
            ->addTableColumn(['title' => '应用', 'field' => 'title', 'minWidth' => 120])
            ->addTableColumn(['title' => '采购时长', 'field' => 'month', 'minWidth' => 100])
            ->addTableColumn(['title' => '订单金额', 'field' => 'total', 'minWidth' => 100])
            ->addTableColumn(['title' => '钱包抵扣', 'field' => 'wallet', 'minWidth' => 100])
            ->addTableColumn(['title' => '现金支付', 'field' => 'amount', 'minWidth' => 100])
            ->addTableColumn(['title' => '订单状态', 'field' => 'order_status', 'minWidth' => 100])
            ->addTableColumn(['title' => '下单时间', 'field' => 'create_time', 'minWidth' => 180])
            ->addTableColumn(['title' => '付款时间', 'field' => 'pay_time', 'minWidth' => 180]);
        return $builder->show();
    }
}