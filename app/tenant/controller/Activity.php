<?php
/**
 * SCRIPT_NAME: Activity.php
 * Created by PhpStorm.
 * Time: 2020/9/6 23:23
 * Description: 活动管理
 * @author: fudaoji <fdj@kuryun.cn>
 */
namespace app\tenant\controller;

use app\common\model\CpActivity;
use app\common\model\CpGoods;
use app\common\service\CpActivity as activityService;
use app\common\service\CpGoods as goodsService;
use app\common\service\Tenant as TenantService;
use app\TenantController;
use ky\Jtx\JdApi\JdApi;
use support\View;

class Activity extends TenantController
{
    /**
     * @var CpActivity
     */
    protected $model;
    /**
     * @var CpGoods
     */
    private $goodsM;

    public function __construct(){
        parent::__construct();
        $this->model = new CpActivity();
        $this->goodsM = new CpGoods();
    }

    /**
     * 添加
     * @throws \think\db\exception\DbException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function add(){
        $published = TenantService::getTodayPublishNum();
        $limit = $this->tenantExtendInfo('cp_limit');
        if(request()->isPost()){
            if($limit <= $published){
                return $this->error('进入发布数量已达上限');
            }
            $post_data = input('post.');
            $self_cid_list = [];
            $pop_cid_list = [];

            if(!empty($post_data['self_cate']) && in_array($post_data['goods_type'], [-1, 1])){
                foreach ($post_data['self_cate'] as $k => $cate1){
                    $temp = ['commissionRateMin' => $post_data['self_rate'][$k]];
                    $cate_arr = explode('|', $cate1);
                    foreach ($cate_arr as $cate){
                        $_cate = explode('_', $cate);
                        $temp[$_cate[0]] = $_cate[1];
                    }
                    array_push($self_cid_list, $temp);
                }
            }

            if(!empty($post_data['pop_cate']) && in_array($post_data['goods_type'], [-1, 2])){
                foreach ($post_data['pop_cate'] as $k => $cate1){
                    $temp = ['commissionRateMin' => $post_data['pop_rate'][$k]];
                    $cate_arr = explode('|', $cate1);
                    foreach ($cate_arr as $cate){
                        $_cate = explode('_', $cate);
                        $temp[$_cate[0]] = $_cate[1];
                    }
                    array_push($pop_cid_list, $temp);
                }
            }

            $jd_data = [
                'type' => 1,
                "shipping" => -1,
                "shopScoreMin" => 1,
                'title' => $post_data['title'],
                "startTime" => date('Y-m-d', strtotime($post_data['start_time'])),
                "endTime" => date('Y-m-d', strtotime($post_data['end_time'])),
                "dongdong" => $post_data['dongdong'],
                "qq" => $post_data['qq'],
                //"selfCidList" => [["category1" => 1315,"commissionRateMin" => "2.9"], ["category1" => 1320,"commissionRateMin" => "2.9"]],
                //"popCidList" => [["category1" => 1315,"commissionRateMin" => "2.9"], ["category1" => 1320,"commissionRateMin" => "2.9"]],
                "goodsType" => $post_data['goods_type'],
                "priceMin" => $post_data['price_min'],
                "priceMax" => $post_data['price_max'],
                "coupon" => $post_data['coupon'],
                "weeklySales" => $post_data['weekly_sales'] ?: -1,
                "favorableRate" => $post_data['favorable_rate'] ?: -1,
                "evaluationCnt" => $post_data['evaluation_cnt'] ?: -1,
                "purchase" => $post_data['purchase'],
                "jdLogistics" => $post_data['jd_logistics'],
                "freightInsurance" => $post_data['freight_insurance'],
                "jdGoodShop" => $post_data['jd_good_shop'],
                "estimateSales" => $post_data['estimate_sales'],
                "status" => (int)config('app.debug') ? 0 : 1,
            ];
            if(!empty($self_cid_list)){
                $jd_data['selfCidList'] = $self_cid_list;
                $jd_data['serviceRateMin'] = $post_data['service_min'];
            }
            if(!empty($pop_cid_list)){
                $jd_data['popCidList'] = $pop_cid_list;
                $jd_data['popServiceRateMin'] = $post_data['pop_service_min'];
            }

            $res = activityService::addActivity(array_merge($jd_data, [
                'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
            ]));
            if(is_string($res)){
                return $this->error($res,'', $jd_data);
            }
            if(empty($res['code'])){
                return $this->error($res['errmsg'],'', $jd_data);
            }
            cache(md5($jd_data['title'].$jd_data['startTime'].$jd_data['endTime']), $this->tenantInfo('id'), 600);
            /*var_dump($jd_data['title'].$jd_data['startTime'].$jd_data['endTime']);
            var_dump(md5($jd_data['title'].$jd_data['startTime'].$jd_data['endTime']));*/
            activityService::pull(['leader_id' => TenantService::getLeaderId($this->tenantInfo())]);
            return $this->success('发布成功','');
        }

        if(! $cate_tree = cache('queryPromotingCategory')){
            $cates = activityService::getCpCategory(['leader_id' => TenantService::getLeaderId($this->tenantInfo())]);
            foreach ($cates['data'] as &$v){
                if(!empty($v['children'])){
                    $_children1 = [];
                    foreach ($v['children'] as $v1){
                        $v1['pid'] = $v['id'];
                        $v1['title'] = $v1['name'];
                        if(!empty($v1['children'])){
                            $_children2 = [];
                            foreach ($v1['children'] as $v2){
                                $v2['title'] = $v2['name'];
                                $v2['pid'] = $v1['id'];
                                $v2['ppid'] = $v1['pid'];
                                $_children2[$v2['id']] = $v2;
                            }
                            $v1['children'] = $_children2;
                        }
                        $_children1[$v1['id']] = $v1;
                    }
                    $v['children'] = $_children1;
                }
                $v['title'] = $v['name'];
                $cate_tree[$v['id']] = $v;
            }
            cache('queryPromotingCategory', $cate_tree, 86400);
        }
        $assign = [
            'tip' => str_replace(['TOTAL','PUBLISHED','REMAIN'], [$limit, $published, $limit - $published], '您今日一共可发：TOTAL条，已发：PUBLISHED条，剩余：REMAIN条'),
            'cates' => $cate_tree
        ];
        return $this->show($assign);
    }

    /**
     * 列表
     * Author: Jason<dcq@kuryun.cn>
     */
    public function index(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['activity.leader_id', '=', TenantService::getLeaderId($this->tenantInfo())]
            ];

            if(empty($post_data['tenant_id'])){
                if(!TenantService::isLeader()){
                    $where[] = $this->tenantWhere();
                }
            }else{
                $where[] = ['goods.tenant_id', '=', $post_data['tenant_id']];
            }
            if(!empty($post_data['activity_id'])){
                $where[] = ['activity.id', '=', $post_data['activity_id']];
            }
            if(!empty($post_data['title'])){
                $where[] = ['activity.title', 'like', '%'.$post_data['title'].'%'];
            }
            if(isset($post_data['status']) && $post_data['status'] > -1) {
                $where[] =['activity.status', '=', $post_data['status']];
            }
            if(!empty($post_data['time_range'])){
                $range = explode('~', $post_data['time_range']);
                $where[] = ['activity.start_time', 'between', [$range[0], $range[1]]];
            }

            $query = $this->model->alias('activity')
                ->where($where)
                ->join('tenant tenant','tenant.id = activity.tenant_id', 'left');
            $total = $query
                ->count();
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order(['update_time' => 'desc', 'start_time' => 'desc'])
                    ->field(['activity.*', 'tenant.realname'])
                    ->select();
                foreach ($list as $k => $v){
                    $v['title'] = '<a href="javascript:;" data-title="活动详情" layuimini-content-href="'.url('goods', ['id' => $v['id']]).'">'.$v['title'].'</a>';
                    $v['sku_info'] = str_replace(['sku_total_cnt', 'sku_examine_cnt', 'sku_cnt'], [$v['sku_total_cnt'],$v['sku_examine_cnt'],$v['sku_cnt']], '<a href="javascript:;" data-title="报名信息" layuimini-content-href="'.url('goods',['id' => $v['id']]).'">报名商品：sku_total_cnt</a><br><a href="javascript:;" data-title="报名信息" layuimini-content-href="'.url('goods',['id' => $v['id'],'status' => 0]).'">待审核：sku_examine_cnt</a><br><a href="javascript:;" data-title="报名信息" layuimini-content-href="'.url('goods',['id' => $v['id'], 'status' => 1]).'">审核通过：sku_cnt</a>');
                    $v['time_info'] = '起：' . $v['start_time'].'<br>止：' . $v['end_time'];
                    $v['effect_info'] = str_replace(['order_cnt', 'estimate_fee', 'service_fee'], [$v['order_cnt_in'],$v['estimate_fee'],$v['service_fee']], '引入订单量：order_cnt<br>预估服务费：estimate_fee<br>实际服务费：service_fee');
                }
            } else {
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list, 'auth_list' => \app\tenant\service\Auth::getAuthList()]);
        }

        $searches = [
            ['type' => 'select', 'name' => 'status', 'title' => '活动状态', 'options' => [-1 => '全部'] + CpActivity::statusList()],
            ['type' => 'text', 'name' => 'title', 'title' => '活动名称','placeholder' => '填写活动名称'],
            ['type' => 'text', 'name' => 'activity_id', 'title' => '活动ID','placeholder' => '填写活动ID'],
            ['type' => 'datetime_range', 'name' => 'time_range', 'title' => '活动时间']
        ];
        if(TenantService::isLeader()){
            $searches[] = ['type' => 'select', 'name' => 'tenant_id', 'title' => '团长', 'options' => [0 => '全部'] + TenantService::getTeamIds('realname','id')];
        }
        $builder = new ListBuilder();
        $builder->setSearch($searches)
            ->addTopButton('addnew')
            ->addTableColumn(['title' => '活动ID', 'field' => 'id', 'minWidth' => 70])
            ->addTableColumn(['title' => '活动名称', 'field' => 'title', 'minWidth' => 90])
            ->addTableColumn(['title' => '活动状态', 'field' => 'status', 'minWidth' => 70, 'type' => 'enum', 'options' => CpActivity::statusList()])
            ->addTableColumn(['title' => '报名商品', 'field' => 'sku_info', 'style' => 'height:60px;'])
            ->addTableColumn(['title' => '活动时间', 'field' => 'time_info'])
            ->addTableColumn(['title' => '活动效果', 'field' => 'effect_info'])
            ->addTableColumn(['title' => '团长', 'field' => 'realname'])
            ->addTableColumn(['title' => '操作', 'minWidth' => 220, 'type' => 'toolbar'])
            ->addRightButton('edit', ['title' => '活动效果', 'href' => url('effect', ['id' => '__data_id__'])])
            ->addRightButton('self', ['copy-text' => 'https://jzt.jd.com/jtk/#/orient-act-info?id=__data_id___7','lay-event' => 'copy','title' => '复制链接','class' => 'layui-btn layui-btn-xs','href' => url('copylink', ['id' => '__data_id__'])])
            ->addRightButton('self', ['target' => '_blank','title' => '导出商品', 'class' => 'layui-btn layui-btn-warm layui-btn-xs','href' => url('exportgoods', ['id' => '__data_id__'])])
            ->setJs('<script src="/static/libs/clipboard/clipboard.min.js"></script>');
        return $builder->show();
    }

    /**
     * 导出商品
     */
    public function exportGoods(){
        $activity_id = input('id', 0);
        $res = activityService::exportCpGoods([
            'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
            'activity_id' => $activity_id
        ]);
        if(! $res['code']){
            return $this->error($res['errmsg']);
        }
        return $this->redirect($res['data']);
    }

    private function activityInfo($tab = 'info'){
        $id = input('id', 0);
        $res = activityService::get([
            'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
            'activity_id' => $id
        ]);
        if(is_string($res)){
            return $res;
        }
        if($res['code']){
            $activity = $res['data'];
        }else{
            return $res['errmsg'];
        }
        if($activity['status'] == 0){
            return '此活动未发布';
        }
        $activity['status_title'] = CpActivity::statusList($activity['status']);
        $tab_nav = [
            'tab_list' => [
                'goods' => ['title' => '报名信息', 'href' => url('goods', ['id' => $id])],
                'effect' => ['title' => '活动效果', 'href' => url('effect', ['id' => $id])],
                'info' => ['title' => '活动信息', 'href' => url('info', ['id' => $id])]
            ],
            'current_tab' => $tab
        ];
        View::assign('activity', $activity);
        View::assign('tab_nav', $tab_nav);
        return ['activity' => $activity, 'tab_nav' => $tab_nav];
    }

    /**
     * 活动效果
     * @throws \think\db\exception\DbException
     */
    public function effect(){
        if(is_string($assign = $this->activityInfo(__FUNCTION__))){
            return $this->error($assign);
        }
        $leader_id = TenantService::getLeaderId($this->tenantInfo());
        $activity_id = input('id', 0);
        if(request()->isPost()){
            $post_data = input('post.');
            $res = activityService::effectGoodsList([
                'leader_id' => $leader_id,
                'activity_id' => $activity_id,
                'page_no' => $post_data['page'],
                'page_size' => $post_data['limit']
            ]);

            if($res['code']){
                $total = $res['data']['totalNum'];
                $list = $res['data']['result'];
                if($total){
                    $tenant_arr = CpGoods::alias('goods')
                        ->where('activity_id', $activity_id)
                        ->join('tenant tenant','tenant.id=goods.tenant_id')
                        ->column('realname', 'sku_id');
                }else{
                    $tenant_arr = [];
                }
                if($total){
                    foreach ($list as $k => $v){
                        $v['realname'] = $tenant_arr[$v['skuId']] ?? '';
                        $v['sku_info'] = str_replace(['sku_name', 'lowest_price', 'discount_price'], [$v['skuName'],$v['lowestPrice']??'',empty($v['discountPrice']) ? '' : ('券后价：￥'.$v['discountPrice'])], '<p>sku_name</p><p>￥lowest_price  <em style="color: red;margin-left: 40px;">discount_price</em></p>');
                        $v['shop_info'] = str_replace(['shop_name'], [$v['shopName']??''], '<p>shop_name</p>');
                        $v['coupon_info'] = empty($v['sendNum']) ? '' : $v['nowCount'] . '/' . $v['sendNum'];
                        $v['time_info'] = $v['startTime'] . '<br>' . $v['endTime'];
                        $v['image_url'] = jd_cdn($v['imageUrl']);
                        $v['service_rate'] = $v['serviceRate'] . '%';
                        $v['estimate_service_fee'] = '￥'.$v['ygServiceFee'];
                        $v['service_fee'] = '￥'.$v['serviceFee'];
                        $v['order_cnt_in'] = $v['orderCntIn'];
                        $v['order_gmv_in'] = '￥'.$v['orderGmvIn'];
                        $list[$k] = $v;
                    }
                }
            }else{
                $total = 0;
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $effect = activityService::getActivityEffect(['leader_id' => $leader_id, 'activity_id' => $activity_id]);
        $effect = $effect['data'];
        $assign['effect'] = $effect;

        $builder = new ListBuilder();
        $builder->addTableColumn(['title' => '商品图片', 'field' => 'image_url', 'type' =>'picture','minWidth' => 70])
            ->addTableColumn(['title' => '商品信息', 'field' => 'sku_info', 'minWidth' => 260])
            ->addTableColumn(['title' => '店铺信息', 'field' => 'shop_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '参与时间', 'field' => 'time_info', 'minWidth' => 150])
            ->addTableColumn(['title' => '服务费率', 'field' => 'service_rate', 'minWidth' => 70])
            ->addTableColumn(['title' => '预估服务费', 'field' => 'estimate_service_fee', 'minWidth' => 90])
            ->addTableColumn(['title' => '实际服务费', 'field' => 'service_fee', 'minWidth' => 90])
            ->addTableColumn(['title' => '有效订单量', 'field' => 'order_cnt_in', 'minWidth' => 90])
            ->addTableColumn(['title' => '有效订单金额', 'field' => 'order_gmv_in', 'minWidth' => 100])
            ->addTableColumn(['title' => '券发放量/券总量', 'field' => 'coupon_info', 'minWidth' => 150])
            ->addTableColumn(['title' => '认领人', 'field' => 'realname', 'minWidth' => 80]);

        return $builder->show($assign, 'common');
    }
    /**
     * 活动信息
     */
    public function info(){
        if(is_string($err = $this->activityInfo(__FUNCTION__))){
            return $this->error($err);
        }
        $assign = [
            'dict' => JdApi::$dict
        ];
        return $this->show($assign, 'common');
    }

    /**
     * 报名信息
     * @throws \think\db\exception\DbException
     */
    public function goods(){
        if(is_string($assign = $this->activityInfo(__FUNCTION__))){
            return $this->error($assign);
        }
        $activity_id = input('id', 0);
        $status = input('status', -1);
        if(request()->isPost()){
            if(! CpGoods::where('activity_id', $activity_id)->count()){
                activityService::pullGoodsList([
                    'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
                    'activity_id' => $activity_id,
                    //'tenant_id' => $assign['activity']['tenant_id']
                ]);
            }
            $post_data = input('post.');
            $where = [
                'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
                'activity_id' => $activity_id,
                'page_no' => $post_data['page'],
                'page_size' => $post_data['limit']
            ];
            if(!empty($post_data['sku_id'])){
                $where['sku_id'] = $post_data['sku_id'];
            }
            if(isset($post_data['status']) && $post_data['status'] > -1) {
                $where['status'] = $post_data['status'];
            }else{
                $where['status'] = $status;
            }

            $res = activityService::signGoodsList($where);
            $total = $res['data']['totalNum'];
            $list = $res['data']['result'];

            $sku_arr = [];
            foreach ($list as $k => $v){ //查询京东
                $sku_arr[] = $v['skuId'];
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

            //var_dump($list);
            if($total){
                $tenant_arr = CpGoods::alias('goods')
                    ->where('activity_id', $activity_id)
                    ->join('tenant tenant','tenant.id=goods.tenant_id')
                    ->column('realname', 'sku_id');
            }else{
                $tenant_arr = [];
            }
            $fields = CpGoods::FIELD_MAP;
            foreach ($list as $k => $v){
                foreach ($v as $k1 => $v1){
                    isset($fields[$k1]) && $v[$fields[$k1]] = ($fields[$k1] == 'image_url' ? 'https://img14.360buyimg.com/n1/' . $v1 : $v1);
                }
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

                $v['realname'] = $tenant_arr[$v['sku_id']] ?? '';
                $v['sku_info'] = str_replace(['goods_url','sku_name', 'lowest_price', 'discount_price'], [$url, cut_str($v['sku_name'], 14), $v['lowest_price']??'', empty($v['discount_price']) ? '' : ('券后价：￥'.$v['discount_price'])], '<p><a href="goods_url" target="_blank">sku_name</a></p><p>￥lowest_price  <em style="color: red;margin-left: 40px;">discount_price</em></p>');
                $v['shop_info'] = str_replace(['shop_name', 'dongdong'], [$v['shop_name']??"",$v['dongdong']??''], '<p>shop_name</p><p>联系方式：dongdong</p>');
                $v['coupon_info'] = empty($v['coupon_start_date']) ? '无' :
                    str_replace(['coupon_amount', 'now_count', 'send_num','coupon_start','coupon_end'],
                        [$v['coupon_amount'],$v['now_count'],$v['send_num'],$v['coupon_start_date'],$v['coupon_end_date']],
                        '<p>券额度：coupon_amount</p><p>发放/总量：now_count/send_num</p><p>使用期限：coupon_start-coupon_end</p>'
                    );
                $v['time_info'] = $v['start_time'] . '<br>' . $v['end_time'];
                $v['service_info'] = (empty($v['lowest_price']) ? '' :fa_money_format($v['lowest_price'] * $v['service_rate']/100));
                $v['commission_info'] = '现佣金:<span style="'.($commission_share > ($v['commission_rate']+$v['service_rate']) ? 'color:red;' : '').'">'.$commission_share.'%</span><br>报名佣金:'.$v['commission_rate'].'%';
                $v['service_rate'] .= '%';
                $v['effect_info'] = '月推广量:' . $order_count_30.'<br>月支出佣金:￥'.$order_comm_30;
                $list[$k] = $v;
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'sku_id', 'title' => 'skuId','placeholder' => '填写商品skuId'],
            ['type' => 'select', 'name' => 'status', 'title' => '商品状态', 'options' => [-1 => '全部'] + CpGoods::statusList(), 'value' => $status],
        ])
            ->setTip('请您谨慎进行商品审核，每个团活动单日审核通过的商品数不能超过100个')
            ->addTableColumn(['title' => '商品图片', 'field' => 'image_url', 'type' =>'picture','minWidth' => 70])
            ->addTableColumn(['title' => '商品信息', 'field' => 'sku_info', 'minWidth' => 260])
            ->addTableColumn(['title' => '店铺信息', 'field' => 'shop_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '优惠券', 'field' => 'coupon_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'minWidth' => 70, 'type' => 'enum', 'options' => CpGoods::statusList()])
            ->addTableColumn(['title' => '参与时间', 'field' => 'time_info', 'minWidth' => 150])
            ->addTableColumn(['title' => '佣金比例', 'field' => 'commission_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '服务费比例', 'field' => 'service_rate', 'minWidth' => 100])
            ->addTableColumn(['title' => '预估服务费', 'field' => 'service_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '30天效果', 'field' => 'effect_info', 'minWidth' => 100])
            ->addTableColumn(['title' => '认领人', 'field' => 'realname', 'minWidth' => 80])
            ->addTableColumn(['title' => '操作', 'minWidth' => 180, 'type' => 'toolbar'])
            ->addRightButton('self', ['title' => '认领', 'data-ajax' => 1,'data-confirm' => 1,'class' => 'layui-btn layui-btn-xs js-rl hide','href' => url('goodsverifypost', ['status' => 1])])
            ->addRightButton('self', ['title' => '通过并认领', 'data-ajax' => 1,'data-confirm' => 1,'class' => 'layui-btn layui-btn-xs js-verify','href' => url('goodsverifypost', ['status' => 1])])
            ->addRightButton('self', ['title' => '拒绝', 'data-ajax' => 1,'data-confirm' => 1,'class' => 'layui-btn layui-btn-danger layui-btn-xs js-verify','href' => url('goodsverifypost', ['status' => 2])]);

        return $builder->show($assign, 'common');
    }

    /**
     * 审核商品
     * @return \support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public function goodsVerifyPost(){
        if(request()->isPost()){
            $post_data = input('post.');
            $status = intval(input('get.status'));
            if($post_data['examineStatus'] == 2){
                return $this->error('该商品已审核！');
            }
            $update = ['tenant_id' => $this->tenantInfo('id')];
            if($post_data['examineStatus'] == 0){
                $params = [
                    'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
                    'activityId' => $post_data['activityId'],
                    'status' => $status,
                    'verifyList' => [
                        ['skuId' => $post_data['sku_id'], 'adownerId' => $post_data['adownerId']]
                    ]
                ];
                //return $this->error('test','', $params);
                if(is_string($res = activityService::verifyGoods($params))){
                    return $this->error($res);
                }
                if(empty($res['code'])){
                    return $this->error($res['errmsg']);
                }
                $update['status'] = $status;
            }
            $this->goodsM->where('activity_id', $post_data['activityId'])
                    ->where('sku_id', $post_data['sku_id'])
                    ->update($update);
            return $this->success('操作成功');
        }
    }

    /**
     * 保存数据
     * @param $request
     * @param string $url
     * @param array $data
     * @return mixed
     * @Author  Doogie<461960962@qq.com>
     */
    public function savePost($request, $url='', $data=[]){
        $post_data = input('post.');
        if(!empty($post_data['password'])){
            $post_data['password'] = fa_generate_pwd($post_data['password']);
        }
        return parent::savePost($request, $url, $post_data);
    }
}