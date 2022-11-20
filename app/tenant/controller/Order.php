<?php
/**
 * Created by PhpStorm.
 * Script Name: Order.php
 * Create: 2022/10/18 14:02
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\common\model\CpOrder;
use app\TenantController;
use app\common\model\CpActivity;
use app\common\model\CpGoods;
use app\common\service\CpActivity as activityService;
use app\common\service\Tenant as TenantService;
use app\common\constant\Jd;

class Order extends TenantController
{
    /**
     * @var CpOrder
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new CpOrder();
    }

    /**
     * 已结算业绩
     * @return mixed|\support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function index(){
        $begin_time_init = date('Y-m-d', strtotime('-7 days', time()));
        $end_time_init = date('Y-m-d', strtotime('+1 day', time()));
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['order.leader_id','=',TenantService::getLeaderId()]
            ];
            $order_field = 'order_time';

            if(empty($post_data['tenant_id'])){
                if(!TenantService::isLeader()){
                    $where[] = $this->tenantWhere('goods');
                }
            }else{
                $where[] = ['goods.tenant_id', '=', $post_data['tenant_id']];
            }

            if(!empty($post_data['activity_id'])){
                $where[] = ['order.activity_id', '=',$post_data['activity_id']];
            }
            if(!empty($post_data['sku_id'])){
                $where[] = ['order.sku_id','=', $post_data['sku_id']];
            }
            if(isset($post_data['status']) && $post_data['status'] > -1) {
                $where[] = $post_data['status'] ? ['valid_code', '=', $post_data['status']] : ['valid_code', 'notin', [15,16,17]];
            }
            if(!empty($post_data['order_id'])){
                $where[] = ['order_id', '=', $post_data['order_id']];
            }
            if(!empty($post_data['order_time'])){
                $time_range = explode('~', str_replace(' ','', $post_data['order_time']));
                $where[] = ['order_time', 'between', [$time_range[0], date('Y-m-d', strtotime($time_range[1])+86400)]];
            }else{
                empty($post_data['finish_time']) && $where[] = ['order_time', 'between', [$begin_time_init, $end_time_init]];
            }
            if(!empty($post_data['finish_time'])){
                $time_range = explode('~', str_replace(' ','', $post_data['finish_time']));
                $where[] = ['finish_time', 'between', [$time_range[0], date('Y-m-d', strtotime($time_range[1])+86400)]];
                $order_field = 'finish_time';
            }
            if(!empty($time_range) && strtotime($time_range[1])-strtotime($time_range[0]) > 31 * 86400){
                return $this->error('时间范围不能超过31天');
            }

            $query = $this->model->alias('order')
                ->where($where)
                ->join('cp_activity activity','activity.id=order.activity_id', 'left')
                ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
                ->join('tenant tenant','tenant.id = goods.tenant_id', 'left');
            $total = $query->count();
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order([$order_field => 'desc'])
                    ->field(['order.final_rate','order.commission_rate', 'order.valid_code','order.sku_name','order.price',
                        'order.sku_owner','order.order_id','order.plus','order.trace_type', 'order.sku_img_url',
                        'order.order_time','order.finish_time','order.pay_month',  'order.union_tag','order.activity_id',
                        'order.sku_num', 'order.sku_frozen_num',  'order.sku_return_num', 'order.sku_shop_name',
                        'order.estimate_cos_price','order.estimate_fee','order.actual_cos_price',  'order.actual_fee',
                        'tenant.realname','activity.title as title', 'order.sku_id'
                    ])
                    ->select();

                foreach ($list as $k => $v){
                    $v['final_rate'] = $v['final_rate'] . '%';
                    $v['commission_rate'] = $v['commission_rate'] . '%';
                    $v['status'] = Jd::orderStatus($v['valid_code']);
                    $v['sku_info'] = str_replace(['sku_name', 'price', 'owner', 'shop_name'], [cut_str($v['sku_name'], 14), $v['price'], Jd::owners($v['sku_owner']),$v['sku_shop_name']], '<p>sku_name</p><p style="color: red;">￥price</p><p><span class="jd-label">owner</span>shop_name</p>');
                    $v['order_id'] = str_replace(['order_id', 'plus', 'trace_type'], [$v['order_id'],Jd::plus($v['plus'])?'<span class="jd-label">plus</span>':'', Jd::traceTypes($v['trace_type'])], '<p>order_id</p><p>plus<span class="jd-label">trace_type</span></p>');
                    $v['act_info'] = 'skuID:'.$v['sku_id'] . '<br>活动ID:'.$v['activity_id'];
                    $v['time_info'] = '下单时间:'.$v['order_time'] . '<br>完成时间:' . ($v['finish_time']?:'--'). '<br>结算时间:' . ($v['pay_month']? date('Y-m-d', strtotime($v['pay_month'])):'--');
                    $v['number_info'] = '商品数量:'.$v['sku_num'].'<br>售后数量:'.$v['sku_frozen_num'].'<br>退货数量:'.$v['sku_return_num'];
                    $v['union_tag'] = Jd::unionTags($v['union_tag']);
                    $list[$k] = $v;
                }

                $total_row = true;
            }else{
                $list = [];
                $total_row = null;
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list,
                'total_row' => $total_row, 'total_row_url' => url('indexTotalPost'),
                'total_row_where' => $where
            ]);
        }

        $teams = TenantService::getTeamIds('realname','id');
        $time_range_init = $begin_time_init.' ~ '. $end_time_init;
        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'select', 'name' => 'status', 'title' => '订单状态', 'options' => [-1 => '全部'] + Jd::orderStatus()],
            ['type' => 'complex', 'name' => 'complex', 'title' => '时间范围', 'value' => $time_range_init, 'defaultSelected' => 'order_time', 'options' => [
                ['type' => 'date','name' => 'order_time','elemName' => '下单时间', 'config' => ['type' => 'date', 'value' => $time_range_init]],
                ['type' => 'date','name' => 'finish_time','elemName' => '完成时间', 'config' => ['type' => 'date']]]
            ],
            ['type' => 'text', 'name' => 'order_id', 'title' => '订单号','placeholder' => '填写订单号'],
            ['type' => 'text', 'name' => 'activity_id', 'title' => '活动Id','placeholder' => '填写活动Id'],
            ['type' => 'text', 'name' => 'sku_id', 'title' => 'skuId','placeholder' => '商品skuId'],
            ['type' => 'select', 'name' => 'tenant_id', 'title' => '认领人', 'options' => [0 => '全部'] + $teams]
        ])
            ->addTableColumn(['title' => 'ID', 'field' => 'act_info', 'minWidth' => 180])
            ->addTableColumn(['title' => '商品图片', 'field' => 'sku_img_url', 'type' =>'picture','minWidth' => 60])
            ->addTableColumn(['title' => '商品信息', 'field' => 'sku_info', 'minWidth' => 260])
            ->addTableColumn(['title' => '订单号', 'field' => 'order_id', 'minWidth' => 130])
            ->addTableColumn(['title' => '订单状态', 'field' => 'status', 'minWidth' => 70])
            ->addTableColumn(['title' => '时间', 'field' => 'time_info', 'minWidth' => 230])
            ->addTableColumn(['title' => '预估计佣金额', 'field' => 'estimate_cos_price', 'minWidth' => 80])
            ->addTableColumn(['title' => '预估服务费', 'field' => 'estimate_fee', 'minWidth' => 80])
            ->addTableColumn(['title' => '服务费率', 'field' => 'commission_rate', 'minWidth' => 80])
            ->addTableColumn(['title' => '分成比例', 'field' => 'final_rate', 'minWidth' => 80])
            ->addTableColumn(['title' => '实际计佣金额', 'field' => 'actual_cos_price', 'minWidth' => 80])
            ->addTableColumn(['title' => '实际服务费', 'field' => 'actual_fee', 'minWidth' => 80])
            ->addTableColumn(['title' => '订单类型', 'field' => 'union_tag', 'minWidth' => 80])
            ->addTableColumn(['title' => '认领人', 'field' => 'realname'])
            ->setCss(".jd-label{display: inline-block;height: 16px;line-height: 16px;font-size: 12px;background: #333;color: #f5a623;border-radius: 4px;padding: 0 3px;margin-left: 2px;}");

        return $builder->show(['total_row' => true]);
    }

    /**
     * 订单明细数据统计
     * @return \support\Response
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function indexTotalPost(){
        $where = input('where');
        $query = $this->model->alias('order')
            ->where($where)
            ->join('cp_activity activity','activity.id=order.activity_id', 'left')
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id', 'left');
        $total_row = [
            'sku_info' => '<div class="layui-row"><div class="layui-col-xs3">有效订单：' . $query->where('valid_code', 'in', [16, 17])->count('order.id') . '</div>',
            'order_id' => '<div class="layui-col-xs3">预估计佣金额：￥' . $query->sum('order.estimate_cos_price') . '</div>',
            'time_info' => '<div class="layui-col-xs3">预估服务费：￥' . $query->sum('order.estimate_fee') . '</div></div>'
        ];
        return $this->success('', '', ['total_row_text' => implode("", $total_row)]);
    }

    /**
     * 订单汇总
     * @throws \think\db\exception\DbException
     */
    public function summary(){
        $begin_time_init = date('Y-m-d', strtotime('-7 days', time()));
        $end_time_init = date('Y-m-d', strtotime('+1 day', time()));
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['order.leader_id','=',TenantService::getLeaderId()],
                ['order.valid_code','in', [16, 17]]
            ];

            if(empty($post_data['tenant_id'])){
                if(!TenantService::isLeader()){
                    $where[] = $this->tenantWhere('goods');
                }
            }else{
                $where[] = ['goods.tenant_id', '=', $post_data['tenant_id']];
            }

            if(!empty($post_data['activity_id'])){
                $where[] = ['order.activity_id', '=',$post_data['activity_id']];
            }
            if(!empty($post_data['sku_id'])){
                $where[] = ['order.sku_id','=', $post_data['sku_id']];
            }
            if(!empty($post_data['product_id'])){
                $where[] = ['order.sku_product_id','=', $post_data['product_id']];
            }
            if(isset($post_data['status']) && $post_data['status'] > -1) {
                $where[] = $post_data['status'] ? ['valid_code', '=', $post_data['status']] : ['valid_code', 'notin', [15,16,17]];
            }
            if(!empty($post_data['sku_shop_name'])){
                $where[] = ['sku_shop_name', 'like', '%'.$post_data['sku_shop_name'].'%'];
            }
            if(!empty($post_data['order_time'])){
                $time_range = explode('~', str_replace(' ','', $post_data['order_time']));
                $where[] = ['order_time', 'between', [$time_range[0], date('Y-m-d', strtotime($time_range[1])+86400)]];
            }else{
                $where[] = ['order_time', 'between', [$begin_time_init, $end_time_init]];
            }

            if(!empty($time_range) && strtotime($time_range[1])-strtotime($time_range[0]) > 31 * 86400){
                return $this->error('时间范围不能超过31天');
            }

            $query = $this->model->alias('order')
                ->where($where)
                //->join('cp_activity activity','activity.id=order.activity_id', 'left')
                ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
                ->join('tenant tenant','tenant.id = goods.tenant_id', 'left')
                ->group('order.sku_id');
            $total = $query
                ->count();

            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order(['order_num' => 'desc'])
                    ->field([
                        'order.sku_id','order.sku_name','sku_img_url','order.sku_owner','order.sku_shop_name','order.activity_id',
                        'order.price','tenant.realname','order.sku_product_id',
                        'COUNT(order.id) AS order_num', 'SUM(order.estimate_cos_price) AS order_gmv',
                        'SUM(estimate_fee) as total_estimate_fee',
                        'COUNT(actual_cos_price > 0.00 OR NULL) AS total_settle_num','SUM(order.actual_cos_price) AS total_actual_price',
                        'SUM(actual_fee) as total_actual_fee',
                    ])
                    ->select();

                //var_dump($query->getLastSql());
                foreach ($list as $k => $v){
                    $v['order_gmv']  = '￥'. $v['order_gmv'];
                    $v['total_estimate_fee']  = '￥'. $v['total_estimate_fee'];
                    $v['total_actual_price']  = '￥'. $v['total_actual_price'];
                    $v['total_actual_fee']  = '￥'. $v['total_actual_fee'];
                    $v['id_info'] = 'skuId:'.$v['sku_id'] . '<br>活动ID:' . $v['activity_id'];
                    $v['sku_info'] = str_replace(['sku_name', 'price', 'owner', 'shop_name'], [cut_str($v['sku_name'], 14), $v['price'], Jd::owners($v['sku_owner']),$v['sku_shop_name']], '<p>sku_name</p><p style="color: red;">￥price</p><p><span class="jd-label">owner</span>shop_name</p>');
                    $list[$k] = $v;
                }

            }else{
                $list = [];
                $total_row = null;
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $teams = TenantService::getTeamIds('realname','id');
        $time_range_init = $begin_time_init.' ~ '. $end_time_init;
        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'date_range','name' => 'order_time','title' => '下单时间', 'value' => $time_range_init],
            ['type' => 'text', 'name' => 'activity_id', 'title' => '活动Id','placeholder' => '填写活动Id'],
            ['type' => 'text', 'name' => 'sku_id', 'title' => 'skuId','placeholder' => '商品skuId'],
            ['type' => 'text', 'name' => 'product_id', 'title' => '商品主Id','placeholder' => '商品主Id'],
            ['type' => 'text', 'name' => 'shop_name', 'title' => '店铺名称','placeholder' => '店铺名称'],
            ['type' => 'select', 'name' => 'tenant_id', 'title' => '认领人', 'options' => [0 => '全部'] + $teams]
        ])
            ->addTableColumn(['title' => 'ID', 'field' => 'id_info', 'minWidth' => 180])
            ->addTableColumn(['title' => '商品图片', 'field' => 'sku_img_url', 'type' =>'picture','minWidth' => 60])
            ->addTableColumn(['title' => '商品信息', 'field' => 'sku_info', 'minWidth' => 260])
            ->addTableColumn(['title' => '付款笔数', 'field' => 'order_num', 'minWidth' => 70])
            ->addTableColumn(['title' => '付款金额', 'field' => 'order_gmv', 'minWidth' => 70])
            ->addTableColumn(['title' => '付款总服务费', 'field' => 'total_estimate_fee', 'minWidth' => 100])
            ->addTableColumn(['title' => '结算笔数', 'field' => 'total_settle_num', 'minWidth' => 100])
            ->addTableColumn(['title' => '结算金额', 'field' => 'total_actual_price', 'minWidth' => 100])
            ->addTableColumn(['title' => '结算总服务费', 'field' => 'total_actual_fee', 'minWidth' => 100])
            ->addTableColumn(['title' => '认领人', 'field' => 'realname'])
            ->setCss(".jd-label{display: inline-block;height: 16px;line-height: 16px;font-size: 12px;background: #333;color: #f5a623;border-radius: 4px;padding: 0 3px;margin-left: 2px;}");

        return $builder->show();
    }

    /**
     * 订单明细
     * @throws \think\db\exception\DbException
     */
    public function index1(){
        $begin_time_init = date('Y-m-d', strtotime('-7 days', time()));
        $end_time_init = date('Y-m-d', strtotime('+1 day', time()));
        if(request()->isPost()){
            $post_data = input('post.');
            $search_where = [
                ['order.leader_id','=',TenantService::getLeaderId()]
            ];
            $where = [];
            $order_field = 'order.order_time';

            if(empty($post_data['tenant_id'])){
                if(!TenantService::isLeader()){
                    $where[] = $this->tenantWhere('goods');
                }
            }else{
                $where[] = ['goods.tenant_id', '=', $post_data['tenant_id']];
            }

            if(!empty($post_data['activity_id'])){
                $search_where[] = ['order.activity_id', '=',$post_data['activity_id']];
            }
            if(!empty($post_data['sku_id'])){
                $search_where[] = ['order.sku_id','=', $post_data['sku_id']];
            }
            if(isset($post_data['status']) && $post_data['status'] > -1) {
                $search_where[] = $post_data['status'] ? ['order.valid_code', '=', $post_data['status']] : ['order.valid_code', 'notin', [15,16,17]];
            }
            if(!empty($post_data['order_id'])){
                $search_where[] = ['order.order_id', '=', $post_data['order_id']];
            }
            if(!empty($post_data['order_time'])){
                $time_range = explode('~', str_replace(' ','', $post_data['order_time']));
                $search_where[] = ['order.order_time', 'between', [$time_range[0], date('Y-m-d', strtotime($time_range[1])+86400)]];
            }else{
                empty($post_data['finish_time']) && $search_where[] = ['order_time', 'between', [$begin_time_init, $end_time_init]];
            }
            if(!empty($post_data['finish_time'])){
                $time_range = explode('~', str_replace(' ','', $post_data['finish_time']));
                $search_where[] = ['finish_time', 'between', [$time_range[0], date('Y-m-d', strtotime($time_range[1])+86400)]];
                $order_field = 'finish_time';
            }
            if(!empty($time_range) && strtotime($time_range[1])-strtotime($time_range[0]) > 31 * 86400){
                return $this->error('时间范围不能超过31天');
            }

            $o = $this->model->alias('order')
                ->field('id')
                ->where($search_where)
                ->buildSql();
            $query = $this->model->alias('order')
                ->where($where)
                ->join($o.' order_slim','order.id=order_slim.id')
                ->join('cp_activity activity','activity.id=order.activity_id', 'left')
                ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
                ->join('tenant tenant','tenant.id = goods.tenant_id', 'left');

            $total = $query->count('order.id');
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order([$order_field => 'desc'])
                    ->field(['order.final_rate','order.commission_rate', 'order.valid_code','order.sku_name','order.price',
                        'order.sku_owner','order.order_id','order.plus','order.trace_type', 'order.sku_img_url',
                        'order.order_time','order.finish_time','order.pay_month',  'order.union_tag',
                        'order.sku_num', 'order.sku_frozen_num',  'order.sku_return_num',
                        'order.estimate_cos_price','order.estimate_fee','order.actual_cos_price',  'order.actual_fee',
                        'tenant.realname','activity.title as title'
                    ])
                    ->select();

                foreach ($list as $k => $v){
                    $v['final_rate'] = $v['final_rate'] . '%';
                    $v['commission_rate'] = $v['commission_rate'] . '%';
                    $v['status'] = Jd::orderStatus($v['valid_code']);
                    $v['sku_info'] = str_replace(['sku_name', 'price', 'owner', 'shop_name'], [$v['sku_name'], $v['price'], Jd::owners($v['sku_owner']),$v['sku_shop_name']], '<p>sku_name</p><p style="color: red;">￥price</p><p><span class="jd-label">owner</span>shop_name</p>');
                    $v['order_id'] = str_replace(['order_id', 'plus', 'trace_type'], [$v['order_id'],Jd::plus($v['plus'])?'<span class="jd-label">plus</span>':'', Jd::traceTypes($v['trace_type'])], '<p>order_id</p><p>plus<span class="jd-label">trace_type</span></p>');
                    $v['act_info'] = '活动ID:'.$v['activity_id'].'<br>活动名称:'.$v['title'];
                    $v['time_info'] = '下单时间:'.$v['order_time'] . '<br>完成时间:' . ($v['finish_time']?:'--'). '<br>结算时间:' . ($v['pay_month']? date('Y-m-d', strtotime($v['pay_month'])):'--');
                    $v['number_info'] = '商品数量:'.$v['sku_num'].'<br>售后数量:'.$v['sku_frozen_num'].'<br>退货数量:'.$v['sku_return_num'];
                    $v['union_tag'] = Jd::unionTags($v['union_tag']);
                    $list[$k] = $v;
                }

                $total_row = [
                    'sku_img_url' => '合计：',
                    'sku_info' => '有效订单：' . $query->where('order.valid_code', 'in', [16, 17])->count('order.id'),
                    'order_id' => '预估计佣金额：￥' . $query->sum('order.estimate_cos_price'),
                    'time_info' => '预估服务费：￥' . $query->sum('order.estimate_fee')
                ];
            }else{
                $list = [];
                $total_row = null;
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list, 'total_row' => $total_row]);
        }

        $teams = TenantService::getTeamIds('realname','id');
        $time_range_init = $begin_time_init.' ~ '. $end_time_init;
        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'select', 'name' => 'status', 'title' => '订单状态', 'options' => [-1 => '全部'] + Jd::orderStatus()],
            ['type' => 'complex', 'name' => 'complex', 'title' => '时间范围', 'value' => $time_range_init, 'defaultSelected' => 'order_time', 'options' => [
                ['type' => 'date','name' => 'order_time','elemName' => '下单时间', 'config' => ['type' => 'date', 'value' => $time_range_init]],
                ['type' => 'date','name' => 'finish_time','elemName' => '完成时间', 'config' => ['type' => 'date']]]
            ],
            ['type' => 'text', 'name' => 'order_id', 'title' => '订单号','placeholder' => '填写订单号'],
            ['type' => 'text', 'name' => 'activity_id', 'title' => '活动Id','placeholder' => '填写活动Id'],
            ['type' => 'text', 'name' => 'sku_id', 'title' => 'skuId','placeholder' => '商品skuId'],
            ['type' => 'select', 'name' => 'tenant_id', 'title' => '认领人', 'options' => [0 => '全部'] + $teams]
        ])
            ->addTableColumn(['title' => '商品图片', 'field' => 'sku_img_url', 'type' =>'picture','minWidth' => 60])
            ->addTableColumn(['title' => '商品信息', 'field' => 'sku_info', 'minWidth' => 260])
            ->addTableColumn(['title' => '订单号', 'field' => 'order_id', 'minWidth' => 130])
            ->addTableColumn(['title' => '订单状态', 'field' => 'status', 'minWidth' => 70])
            ->addTableColumn(['title' => '时间', 'field' => 'time_info', 'minWidth' => 230])
            ->addTableColumn(['title' => '预估计佣金额', 'field' => 'estimate_cos_price', 'minWidth' => 80])
            ->addTableColumn(['title' => '预估服务费', 'field' => 'estimate_fee', 'minWidth' => 80])
            ->addTableColumn(['title' => '服务费率', 'field' => 'commission_rate', 'minWidth' => 80])
            ->addTableColumn(['title' => '分成比例', 'field' => 'final_rate', 'minWidth' => 80])
            ->addTableColumn(['title' => '实际记佣金额', 'field' => 'actual_cos_price', 'minWidth' => 80])
            ->addTableColumn(['title' => '实际服务费', 'field' => 'actual_fee', 'minWidth' => 80])
            ->addTableColumn(['title' => '活动信息', 'field' => 'act_info', 'minWidth' => 150])
            ->addTableColumn(['title' => '订单类型', 'field' => 'union_tag', 'minWidth' => 80])
            ->addTableColumn(['title' => '认领人', 'field' => 'realname'])
            ->setCss(".jd-label{display: inline-block;height: 16px;line-height: 16px;font-size: 12px;background: #333;color: #f5a623;border-radius: 4px;padding: 0 3px;margin-left: 2px;}");

        return $builder->show(['total_row' => true]);
    }
}