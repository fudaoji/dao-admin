<?php
/**
 * Created by PhpStorm.
 * Script Name: Bonus.php
 * Create: 2022/10/18 14:02
 * Description: 业绩
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\common\model\CpOrder;
use app\TenantController;
use app\common\service\Tenant as TenantService;
use app\common\model\Tenant as TenantM;

class Bonus extends TenantController
{
    /**
     * @var CpOrder
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new CpOrder();
    }

    private function getTabs($get_param = []){
        return [
            'yjs' => ['title' => '结算业绩', 'href' => url('index', array_merge($get_param, ['type' => 'yjs']))],
            'wjs' => ['title' => '预估业绩', 'href' => url('estimate', array_merge($get_param, ['type' => 'wjs']))]
        ];
    }

    /**
     * 预估业绩
     * @throws \think\db\exception\DbException
     */
    public function estimate(){
        $leader_id = TenantService::getLeaderId();
        $begin_time_init = date('Y-m-d', strtotime('-7 days', time()));
        $end_time_init = date('Y-m-d', time());
        $account_id = input('account_id', 0);
        if($account_id){
            session('begin_time') && $begin_time_init = session('begin_time');
            session('end_time') && $end_time_init = session('end_time');
        }
        $tenant_info = $account_id ? TenantM::find($account_id) :$this->tenantInfo();
        $type = 'wjs';
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['order.leader_id','=', $leader_id],
                ['order.valid_code','=', 17]
            ];

            if(empty($post_data['tenant_id'])){
                if(!TenantService::isLeader($tenant_info)){
                    $where[] = $this->tenantWhere('goods', $tenant_info);
                }
            }else{
                $where[] = ['goods.tenant_id', '=', $post_data['tenant_id']];
            }

            if(!empty($post_data['pay_month'])){
                $time_range = explode('~', str_replace(' ','', $post_data['pay_month']));
                $begin_time_init = date('Y-m-d', strtotime($time_range[0]));
                $end_time_init = date('Y-m-d', strtotime($time_range[1])) . ' 23:59:59';
            }
            $where[] = ['order.finish_time', 'between', [$begin_time_init, $end_time_init]];

            session(['begin_time' => $begin_time_init]);
            session(['end_time' => $end_time_init]);

            if(!empty($time_range) && strtotime($time_range[1])-strtotime($time_range[0]) > 31 * 86400){
                return $this->error('时间范围不能超过31天');
            }

            $pop_build = $this->model->where('sku_owner', 'p')->field('id,actual_cos_price,estimate_fee')
                ->where('finish_time', 'between', [$begin_time_init, $end_time_init])
                ->where('leader_id', $leader_id)
                ->buildSql();
            $self_build = $this->model->where('sku_owner', 'g')->field('id,actual_cos_price,estimate_fee')
                ->where('finish_time', 'between', [$begin_time_init, $end_time_init])
                ->where('leader_id', $leader_id)
                ->buildSql();

            $query = $this->model->alias('order')
                ->where($where)
                ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
                ->join('tenant tenant','tenant.id = goods.tenant_id')
                ->join($pop_build . ' pop','pop.id = order.id', 'left')
                ->join($self_build . ' self','self.id = order.id','left')
                ->group('tenant.id');

            $total = $query->count();
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order(['tenant.id' => 'asc'])
                    ->field([
                        'tenant.realname as tenant_name','tenant.create_time as tenant_reg_time','tenant.id as id',
                        'COUNT(order.id) AS order_num', /*'COUNT(order.sku_owner = "p" OR NULL) AS order_num_pop',*/'COUNT(order.sku_owner = "g" OR NULL) AS order_num_self',
                        'SUM(order.actual_cos_price) AS cos_price', /*'SUM(pop.actual_cos_price) AS cos_price_pop',*/ 'SUM(self.actual_cos_price) AS cos_price_self',
                        'SUM(order.estimate_fee) AS estimate_fee', /*'SUM(pop.actual_fee) AS actual_fee_pop',*/'SUM(self.estimate_fee) AS estimate_fee_self'
                    ])
                    ->select();
                foreach ($list as $k => $v){
                    $v['order_num_pop'] = $v['order_num'] - $v['order_num_self'];
                    $v['cos_price_pop'] = $v['cos_price'] - $v['cos_price_self'];
                    $v['estimate_fee_pop'] = $v['estimate_fee'] - $v['estimate_fee_self'];
                    $v['tenant_reg_time'] = date('Y-m-d H:i:s', $v['tenant_reg_time']);
                    $v['order_info']  = '合计:'. $v['order_num'].'<br>自营:' . $v['order_num_self'].'<br>POP:' . $v['order_num_pop'];
                    $v['estimate_info']  = '合计:￥'. $v['cos_price'].'<br>自营:￥' . $v['cos_price_self'].'<br>POP:￥' . $v['cos_price_pop'];
                    $v['actual_info']  = '合计:￥'. $v['estimate_fee'].'<br>自营:￥' . $v['estimate_fee_self'].'<br>POP:￥' . $v['estimate_fee_pop'];
                    $list[$k] = $v;
                }
                //var_dump($query->getLastSql());

                $total_row = true;
            }else{
                $list = [];
                $total_row = null;
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list,
                'total_row' => $total_row, 'total_row_url' => url('estimateTotalPost'),
                'total_row_where' => $where
            ]);
        }

        $teams = TenantService::getTeamIds('realname','id', $tenant_info);
        $time_range_init = date('Y-m-d', strtotime($begin_time_init)).' ~ '. date('Y-m-d', strtotime($end_time_init));
        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'date_range','name' => 'pay_month','title' => '完成时间', 'value' => $time_range_init],
            ['type' => 'select', 'name' => 'tenant_id', 'title' => '认领人', 'options' => [0 => '全部'] + $teams]
        ])
            ->setTabNav($this->getTabs(['account_id' => $account_id]), $type)
            ->setDataUrl(url(__FUNCTION__, ['account_id' => $account_id, 'type' => $type]))
            ->addTableColumn(['title' => '序号', 'type' => 'index', 'field' => 'index', 'minWidth' => 60])
            ->addTableColumn(['title' => '团长', 'field' => 'tenant_name', 'minWidth' => 90])
            ->addTableColumn(['title' => '完成订单数', 'field' => 'order_info', 'minWidth' => 200])
            ->addTableColumn(['title' => '计佣金额', 'field' => 'estimate_info', 'minWidth' => 200])
            ->addTableColumn(['title' => '预估服务费', 'field' => 'actual_info', 'minWidth' => 200])
            ->addTableColumn(['title' => '入驻时间', 'field' => 'tenant_reg_time', 'minWidth' => 180])
            ->addTableColumn(['title' => '操作', 'minWidth' => 100, 'type' => 'toolbar'])
            ->addRightButton('edit', ['title' => '结算明细', 'href' => url('estimate', ['account_id' => '__data_id__'])]);

        return $builder->show(['total_row' => true]);
    }

    /**
     * 预估业绩数据统计
     * @return \support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public function estimateTotalPost(){
        $where = input('where');
        $query = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id');

        $order_num = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->count();
        $order_num_self = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->where('order.sku_owner', 'g')
            ->count();
        $order_num_pop = $order_num - $order_num_self;
        $cos_price = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->sum('order.actual_cos_price');
        $cos_price_self = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->where('order.sku_owner', 'g')
            ->sum('order.actual_cos_price');
        $cos_price_pop = $cos_price - $cos_price_self;
        $actual_fee = $query->sum('order.estimate_fee');
        $actual_fee_self = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->where('order.sku_owner', 'g')
            ->sum('order.estimate_fee');

        $actual_fee_pop = $actual_fee - $actual_fee_self;
        $total_row = [
            'order_info' => '<div class="layui-row"><div class="layui-col-xs3">订单数合计:'. $order_num.'<br>自营:' . $order_num_self.'<br>POP:' . $order_num_pop . '</div>',
            'estimate_info' => '<div class="layui-col-xs3">计佣金额合计:￥'. $cos_price.'<br>自营:￥' . $cos_price_self.'<br>POP:￥' . $cos_price_pop . '</div>',
            'actual_info' => '<div class="layui-col-xs3">预估服务费合计:￥'. $actual_fee.'<br>自营:￥' . $actual_fee_self.'<br>POP:￥' . $actual_fee_pop . '</div></div>'
        ];
        return $this->success('', '', ['total_row_text' => implode("", $total_row)]);
    }

    /**
     * 结算业绩
     * @throws \think\db\exception\DbException
     */
    public function index(){
        $begin_time_init = date('Ymd', strtotime('-7 days', time()));
        $end_time_init = date('Ymd', time());
        $account_id = input('account_id', 0);
        if($account_id){
            session('begin_time') && $begin_time_init = session('begin_time');
            session('end_time') && $end_time_init = session('end_time');
        }
        $tenant_info = $account_id ? TenantM::find($account_id) :$this->tenantInfo();
        $type = 'yjs';
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['order.leader_id','=',TenantService::getLeaderId()],
                ['order.actual_fee','>', 0]
            ];

            if(empty($post_data['tenant_id'])){
                if(!TenantService::isLeader($tenant_info)){
                    $where[] = $this->tenantWhere('goods', $tenant_info);
                }
            }else{
                $where[] = ['goods.tenant_id', '=', $post_data['tenant_id']];
            }

            if(!empty($post_data['pay_month'])){
                $time_range = explode('~', str_replace(' ','', $post_data['pay_month']));
                $begin_time_init = date('Ymd', strtotime($time_range[0]));
                $end_time_init = date('Ymd', strtotime($time_range[1]));
            }
            $where[] = ['order.pay_month', 'between', [$begin_time_init, $end_time_init]];

            session(['begin_time' => $begin_time_init]);
            session(['end_time' => $end_time_init]);

            if(!empty($time_range) && strtotime($time_range[1])-strtotime($time_range[0]) > 31 * 86400){
                return $this->error('时间范围不能超过31天');
            }

            $pop_build = $this->model->where('sku_owner', 'p')
                ->where('pay_month', 'between', [$begin_time_init, $end_time_init])
                ->buildSql();
            $self_build = $this->model->where('sku_owner', 'g')
                ->where('pay_month', 'between', [$begin_time_init, $end_time_init])
                ->buildSql();

            $query = $this->model->alias('order')
                ->where($where)
                ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
                ->join('tenant tenant','tenant.id = goods.tenant_id')
                ->join($pop_build . ' pop','pop.id = order.id', 'left')
                ->join($self_build . ' self','self.id = order.id','left')
                ->group('tenant.id');
            $total = $query
                ->count();

            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order(['tenant.id' => 'asc'])
                    ->field([
                        'tenant.realname as tenant_name','tenant.create_time as tenant_reg_time','tenant.id as id',
                        'COUNT(order.id) AS order_num', /*'COUNT(order.sku_owner = "p" OR NULL) AS order_num_pop',*/'COUNT(order.sku_owner = "g" OR NULL) AS order_num_self',
                        'SUM(order.actual_cos_price) AS cos_price', /*'SUM(pop.actual_cos_price) AS cos_price_pop',*/ 'SUM(self.actual_cos_price) AS cos_price_self',
                        'SUM(order.actual_fee) AS actual_fee', /*'SUM(pop.actual_fee) AS actual_fee_pop',*/'SUM(self.actual_fee) AS actual_fee_self'
                    ])
                    ->select();

                foreach ($list as $k => $v){
                    $v['order_num_pop'] = $v['order_num'] - $v['order_num_self'];
                    $v['cos_price_pop'] = $v['cos_price'] - $v['cos_price_self'];
                    $v['actual_fee_pop'] = $v['actual_fee'] - $v['actual_fee_self'];
                    $v['tenant_reg_time'] = date('Y-m-d H:i:s', $v['tenant_reg_time']);
                    $v['order_info']  = '合计:'. $v['order_num'].'<br>自营:' . $v['order_num_self'].'<br>POP:' . $v['order_num_pop'];
                    $v['estimate_info']  = '合计:￥'. $v['cos_price'].'<br>自营:￥' . $v['cos_price_self'].'<br>POP:￥' . $v['cos_price_pop'];
                    $v['actual_info']  = '合计:￥'. $v['actual_fee'].'<br>自营:￥' . $v['actual_fee_self'].'<br>POP:￥' . $v['actual_fee_pop'];
                    $list[$k] = $v;
                }
                $total_row = true;
            }else{
                $list = [];
                $total_row = null;
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list,
                'total_row' => $total_row, 'total_row_url' => url('actualTotalPost'),
                'total_row_where' => $where
            ]);
        }

        $teams = TenantService::getTeamIds('realname','id', $tenant_info);
        $time_range_init = date('Y-m-d', strtotime($begin_time_init)).' ~ '. date('Y-m-d', strtotime($end_time_init));
        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'date_range','name' => 'pay_month','title' => '结算时间', 'value' => $time_range_init],
            ['type' => 'select', 'name' => 'tenant_id', 'title' => '团长', 'options' => [0 => '全部'] + $teams]
        ])
            ->setTabNav($this->getTabs(['account_id' => $account_id]), $type)
            ->setDataUrl(url('index', ['account_id' => $account_id, 'type' => $type]))
            ->addTableColumn(['title' => '序号', 'type' => 'index', 'field' => 'index', 'minWidth' => 60])
            ->addTableColumn(['title' => '团长', 'field' => 'tenant_name', 'minWidth' => 90])
            ->addTableColumn(['title' => '结算订单数', 'field' => 'order_info', 'minWidth' => 200])
            ->addTableColumn(['title' => '结算金额', 'field' => 'estimate_info', 'minWidth' => 200])
            ->addTableColumn(['title' => '结算服务费', 'field' => 'actual_info', 'minWidth' => 200])
            ->addTableColumn(['title' => '入驻时间', 'field' => 'tenant_reg_time', 'minWidth' => 180])
            ->addTableColumn(['title' => '操作', 'minWidth' => 100, 'type' => 'toolbar'])
            ->addRightButton('edit', ['title' => '结算明细', 'href' => url('index', ['account_id' => '__data_id__'])]);

        return $builder->show(['total_row' => true]);
    }

    /**
     * 已结算业绩数据统计
     * @return \support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public function actualTotalPost(){
        $where = input('where');
        $query = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id');

        $order_num = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->count();
        $order_num_self = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->where('order.sku_owner', 'g')
            ->count();
        $order_num_pop = $order_num - $order_num_self;
        $cos_price = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->sum('order.actual_cos_price');
        $cos_price_self = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->where('order.sku_owner', 'g')
            ->sum('order.actual_cos_price');
        $cos_price_pop = $cos_price - $cos_price_self;
        $actual_fee = $query->sum('order.actual_fee');
        $actual_fee_self = $this->model->alias('order')
            ->where($where)
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('tenant tenant','tenant.id = goods.tenant_id')
            ->where('order.sku_owner', 'g')
            ->sum('order.actual_fee');
        $actual_fee_pop = $actual_fee - $actual_fee_self;
        $total_row = [
            'order_info' => '<div class="layui-row"><div class="layui-col-xs3">结算订单数合计:'. $order_num.'<br>自营:' . $order_num_self.'<br>POP:' . $order_num_pop . '</div>',
            'estimate_info' => '<div class="layui-col-xs3">结算金额合计:￥'. $cos_price.'<br>自营:￥' . $cos_price_self.'<br>POP:￥' . $cos_price_pop . '</div>',
            'actual_info' => '<div class="layui-col-xs3">结算服务费合计:￥'. $actual_fee.'<br>自营:￥' . $actual_fee_self.'<br>POP:￥' . $actual_fee_pop . '</div></div>'
        ];
        return $this->success('', '', ['total_row_text' => implode("", $total_row)]);
    }
}