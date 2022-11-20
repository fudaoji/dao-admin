<?php
/**
 * Created by PhpStorm.
 * Script Name: Goods.php
 * Create: 2022/10/18 14:02
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\TenantController;
use app\common\model\CpActivity;
use app\common\model\CpGoods;
use app\common\service\CpActivity as activityService;
use app\common\service\CpGoods as goodsService;
use app\common\service\Tenant as TenantService;
use ky\Jtx\JdApi\JdApi;

class Goods extends TenantController
{
    /**
     * @var CpGoods
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new CpGoods();
    }

    /**
     * 我的商品
     * @throws \think\db\exception\DbException
     */
    public function index(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['goods.leader_id' ,'=', TenantService::getLeaderId($this->tenantInfo())],
            ];
            if(empty($post_data['tenant_id'])){
                if(!TenantService::isLeader()){
                    $where[] = $this->tenantWhere('goods');
                }
            }else{
                $where[] = ['goods.tenant_id', '=', $post_data['tenant_id']];
            }
            if(!empty($post_data['sku_id'])){
                $where[] = ['sku_id', '=', $post_data['sku_id']];
            }
            if(!empty($post_data['activity_id'])){
                $where[] = ['activity_id', '=', $post_data['activity_id']];
            }
            if(isset($post_data['status']) && $post_data['status'] > -1) {
                $where[] = ['goods.status', '=', $post_data['status']];
            }
            if(!empty($post_data['shop_name'])){
                $where[] = ['shop_name', 'like', '%'.$post_data['shop_name'].'%'];
            }

            $query = $this->model->alias('goods')
                ->where($where)
                ->join('tenant tenant','tenant.id = goods.tenant_id', 'left');

            $total = $query->count();
            if($total){
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order(['id' => 'desc'])
                    ->field(['goods.*', 'tenant.realname'])
                    ->select();
                $sku_arr = [];
                foreach ($list as $k => $v){ //查询京东
                    $sku_arr[] = $v['sku_id'];
                }
                $res = GoodsService::openGoodsQuery([
                    'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
                    'page_no' => $post_data['page'],
                    'page_size' => $post_data['limit'],
                    'sku_ids' => $sku_arr
                ]);
                if(!$res['code']){
                    return $this->error($res['errmsg']);
                }
                $jd_goods = [];
                foreach ($res['list'] as $k => $v){
                    $jd_goods[$v['skuId']] = $v;
                }

                foreach ($list as $k => $v){
                    if(isset($jd_goods[$v['sku_id']])){
                        $g = $jd_goods[$v['sku_id']];
                        $order_comm_30 = $g['inOrderComm30Days'] ?? 0.00;
                        $order_count_30 = $g['inOrderCount30Days'] ?? 0;
                        $commission_share = $g['commissionInfo']['commissionShare'] ?? 0.00;
                        $url = 'http://'.$g['materialUrl'];
                    }else{
                        $order_comm_30 =  0.00;
                        $order_count_30 = 0;
                        $commission_share = 0.00;
                        $url = 'http://item.jd.com/'.$v['sku_id'].'.html';
                    }

                    $v['id_info'] = '活动ID:' . $v['activity_id'] . '<br>' . 'skuId:' . $v['sku_id'];
                    $v['sku_info'] = str_replace(['goods_url','sku_name', 'lowest_price', 'discount_price'], [$url, cut_str($v['sku_name'], 14), $v['lowest_price']??'', empty($v['discount_price']) ? '' : ('券后价：￥'.$v['discount_price'])], '<p><a href="goods_url" target="_blank">sku_name</a></p><p>￥lowest_price  <em style="color: red;margin-left: 40px;">discount_price</em></p>');
                    $v['shop_info'] = str_replace(['shop_name', 'dongdong'], [$v['shop_name']??"",$v['dongdong']??''], '<p>shop_name</p><p>联系方式：dongdong</p>');
                    $v['coupon_info'] = empty($v['coupon_start_date']) ? '无' :
                        str_replace(['coupon_amount', 'now_count', 'send_num','coupon_start','coupon_end'],
                            [$v['coupon_amount'],$v['now_count'],$v['send_num'],$v['coupon_start_date'],$v['coupon_end_date']],
                            '<p>券额度：coupon_amount</p><p>发放/总量：now_count/send_num</p><p>使用期限：coupon_start-coupon_end</p>'
                        );
                    $v['time_info'] = $v['start_time'] . '<br>' . $v['end_time'];
                    $v['service_info'] = '￥'.(empty($v['lowest_price']) ? '' :fa_money_format($v['lowest_price'] * $v['service_rate']/100));
                    $v['commission_info'] = '现佣金:<span style="'.($commission_share > ($v['commission_rate']+$v['service_rate']) ? 'color:red;' : '').'">'.$commission_share.'%</span><br>报名佣金:'.$v['commission_rate'].'%';
                    $v['service_rate'] .= '%';
                    $v['effect_info'] = '月推广量:' . $order_count_30.'<br>月支出佣金:￥'.$order_comm_30;
                    $list[$k] = $v;
                }
            }else{
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $teams = TenantService::getTeamIds('realname','id');
        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'select', 'name' => 'status', 'title' => '商品状态', 'options' => [-1 => '全部'] + CpGoods::statusList()],
            ['type' => 'text', 'name' => 'activity_id', 'title' => '活动Id','placeholder' => '填写活动Id'],
            ['type' => 'text', 'name' => 'sku_id', 'title' => 'skuId','placeholder' => '填写商品skuId'],
            ['type' => 'text', 'name' => 'shop_name', 'title' => '店铺名称','placeholder' => '填写店铺名称'],
            ['type' => 'select', 'name' => 'tenant_id', 'title' => '认领人', 'options' => [0 => '全部'] + $teams]
        ])
            ->addTableColumn(['title' => 'ID', 'field' => 'id_info', 'minWidth' => 180])
            ->addTableColumn(['title' => '商品图片', 'field' => 'image_url', 'type' =>'picture','minWidth' => 70])
            ->addTableColumn(['title' => '商品信息', 'field' => 'sku_info', 'minWidth' => 260])
            ->addTableColumn(['title' => '店铺信息', 'field' => 'shop_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '优惠券', 'field' => 'coupon_info', 'minWidth' => 150])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'minWidth' => 70, 'type' => 'enum', 'options' => CpGoods::statusList()])
            ->addTableColumn(['title' => '参与时间', 'field' => 'time_info', 'minWidth' => 120])
            ->addTableColumn(['title' => '佣金比例', 'field' => 'commission_info', 'minWidth' => 180])
            ->addTableColumn(['title' => '服务费比例', 'field' => 'service_rate', 'minWidth' => 100])
            ->addTableColumn(['title' => '预估服务费', 'field' => 'service_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '30天效果', 'field' => 'effect_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '认领人', 'field' => 'realname'])
            /*->addTableColumn(['title' => '操作', 'minWidth' => 90, 'type' => 'toolbar'])
            ->addRightButton('edit', ['title' => '预估效果'])*/;

        return $builder->show();
    }

    /**
     * 退出申请
     * @throws \think\db\exception\DbException
     */
    public function signOut(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['goods.leader_id','=',TenantService::getLeaderId()],
                ['cancel_id', '>', 0]
            ];

            if(!TenantService::isLeader()){
                $where[] = $this->tenantWhere('goods');
            }

            if(!empty($post_data['activity_id'])){
                $where[] = ['activity_id', '=',$post_data['activity_id']];
            }
            if(!empty($post_data['sku_id'])){
                $where[] = ['sku_id','=', $post_data['sku_id']];
            }
            if(isset($post_data['status']) && $post_data['status'] > -1) {
                $where[] = ['cancel_status', '=', $post_data['status']];
            }
            if(!empty($post_data['shop_name'])){
                $where[] = ['shop_name', '=', $post_data['shop_name']];
            }
            //var_dump($where);
            $query = $this->model->alias('goods')
                ->where($where)
                ->join('tenant tenant','tenant.id = goods.tenant_id', 'left')
                ->join('cp_activity activity','activity.id = goods.activity_id', 'left');
            $total = $query
                ->count();
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order(['cancel_id' => 'desc'])
                    ->field(['goods.*', 'tenant.realname','activity.title as title','goods.cancel_status as status'])
                    ->select();

                foreach ($list as $k => $v){
                    $v['sku_info'] = str_replace(['sku_name', 'lowest_price', 'discount_price'], [$v['sku_name'], $v['price']??'', empty($v['discount_price']) ? '' : ('券后价：￥'.$v['discount_price'])], '<p>sku_name</p><p style="color: red;">￥lowest_price</p>');
                    $v['shop_info'] = str_replace(['shop_name', 'dongdong'], [$v['shop_name'],$v['dongdong']??'无'], '<p>shop_name</p><p>联系方式：dongdong</p>');
                    $v['act_info'] = '活动ID:'.$v['activity_id'].'<br>活动名称:'.$v['title'].'<br>起:' .$v['start_time'] . '<br>止:' . $v['end_time'];
                    $v['apply_info'] = '申请时间：'.$v['cancel_apply_time'] . '<br>申请原因' . $v['cancel_reason'];
                    $list[$k] = $v;
                }
            }else{
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'select', 'name' => 'status', 'title' => '退出状态', 'options' => [-1 => '全部'] + CpGoods::cancelStatusList()],
            ['type' => 'complex', 'name' => 'complex', 'title' => '复合查询', 'options' => [
                ['type' => 'input','name' => 'sku_id','elemName' => 'skuId'],['type' => 'input','name' => 'shop_name','elemName' => '店铺名称']]
            ],
            ['type' => 'text', 'name' => 'activity_id', 'title' => '活动Id','placeholder' => '填写活动Id'],
        ])
            ->setTip('48小时内不处理，则默认通过。')
            ->addTableColumn(['title' => '商品图片', 'field' => 'image_url', 'type' =>'picture','minWidth' => 70])
            ->addTableColumn(['title' => '商品信息', 'field' => 'sku_info', 'minWidth' => 260])
            ->addTableColumn(['title' => '店铺信息', 'field' => 'shop_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '活动信息', 'field' => 'act_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '退出状态', 'field' => 'status', 'minWidth' => 70, 'type' => 'enum', 'options' => CpGoods::cancelStatusList()])
            ->addTableColumn(['title' => '申请信息', 'field' => 'apply_info', 'minWidth' => 150])
            ->addTableColumn(['title' => '认领人', 'field' => 'realname'])
            ->addTableColumn(['title' => '操作', 'minWidth' => 220, 'type' => 'toolbar'])
            ->addRightButton('self', ['title' => '通过', 'confirm' => '审核通过后，T+1生效，生效后，该商品产生的订单，将不再享有服务费。确认吗？','data-ajax' => 1,'data-confirm' => 1,'class' => 'layui-btn layui-btn-xs js-verify','href' => url('verifypost', ['status' => 1])])
            ->addRightButton('self', ['title' => '拒绝', 'data-ajax' => 1,'data-confirm' => 1,'class' => 'layui-btn layui-btn-danger layui-btn-xs js-verify','href' => url('verifypost', ['status' => 2])]);

        return $builder->show([], 'signout');
    }

    /**
     * 审核退出申请
     * @return \support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public function verifyPost(){
        if(request()->isPost()){
            $post_data = input('post.');
            $status = intval(input('get.status'));
            if($post_data['status'] == 6){
                return $this->error('该商品已审核！');
            }
            //审核通过后，T+1生效，生效后，该商品产生的订单，将不再享有服务费。
            $params = [
                'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
                'status' => $status,
                'idList' => [$post_data['cancel_id']],
            ];

            if(is_string($res = activityService::examineCancelApply($params))){
                return $this->error($res);
            }
            if(empty($res['code'])){
                return $this->error($res['errmsg']);
            }else{
                $this->model->update([
                    'id' => $post_data['id'],
                    'cancel_status' => $status
                ]);
                activityService::pullGoodsList([
                    'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
                    'activity_id' => $post_data['activity_id']
                ]);
            }
            return $this->success('操作成功');
        }
    }
}