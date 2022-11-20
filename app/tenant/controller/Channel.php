<?php
/**
 * Created by PhpStorm.
 * Script Name: Goods.php
 * Create: 2022/10/18 14:02
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\common\constant\Jd;
use app\common\model\CpOrder;
use app\common\model\TenantInfo;
use app\TenantController;
use app\common\model\Channel as ChannelM;
use app\common\service\CpChannel as channelService;
use app\common\service\Tenant as TenantService;
use ky\Jtx\JdApi\JdApi;
use think\facade\Db;

class Channel extends TenantController
{
    /**
     * @var \app\common\model\Tenant
     */
    private $tenantM;
    /**
     * @var TenantInfo
     */
    private $tenantInfoM;
    /**
     * @var CpOrder
     */
    private $orderM;
    /**
     * @var ChannelM
     */
    private $channelM;

    public function __construct(){
        parent::__construct();
        $this->tenantM = new \app\common\model\Tenant();
        $this->tenantInfoM = new TenantInfo();
        $this->orderM = new CpOrder();
        $this->channelM = new ChannelM();
    }

    /**
     * 渠道列表
     * Author: Jason<dcq@kuryun.cn>
     */
    public function channels(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['pid', '=', $this->tenantInfo('id')],
                ['group_id', '=', \app\tenant\service\Auth::getChannelGroup('id')]
            ];
            !empty($post_data['search_key']) && $where[] = ['username|mobile|realname', 'like', '%'.$post_data['search_key'].'%'];

            $total = $this->tenantM->where($where)
                ->count();
            if ($total) {
                $list = $this->tenantM->where($where)
                    ->alias('tenant')
                    ->page($post_data['page'], $post_data['limit'])
                    ->order('id', 'desc')
                    ->field(['tenant.*'])
                    ->select();
            } else {
                $list = [];
            }

            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '账号、手机号、名称']
        ])
            ->addTopButton('addnew', ['href' => url('channeladd')])
            ->addTableColumn(['title' => '序号', 'type' => 'index'])
            ->addTableColumn(['title' => '名称', 'field' => 'realname'])
            ->addTableColumn(['title' => '账号', 'field' => 'username'])
            ->addTableColumn(['title' => '手机号', 'field' => 'mobile'])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'enum', 'options' => [0 => '禁用', 1 => '启用']])
            ->addTableColumn(['title' => '操作', 'width' => 220, 'type' => 'toolbar'])
            ->addRightButton('edit', ['href' => url('channeledit', ['id' => '__data_id__'])])
            ->addRightButton('edit', ['title' => '修改密码','class' => 'layui-btn layui-btn-warm layui-btn-xs','href' => url('tenant/setpassword', ['id' => '__data_id__'])])
            ->addRightButton('delete', ['href' => url('tenant/setstatus', ['status' => 'delete'])]);
        return $builder->show();
    }

    /**
     * 编辑渠道账号
     */
    public function channelEdit(){
        $id = input('id');
        $data = $this->tenantM->alias('tenant')
            ->join('tenant_info info','tenant.id = info.id', 'left')
            ->field(['tenant.*'])
            ->find($id);
        if(! $data){
            return $this->error('id参数错误');
        }
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')  //设置页面标题
        ->setPostUrl(url('channelsavepost')) //设置表单提交地址
        ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('realname', 'text', '名称', '名称', [], 'required')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('mobile', 'text', '手机', '手机')
            ->addFormItem('service_rate', 'number', '渠道服务费比例', '请填写0-100之间')
            ->addFormItem('status', 'radio', '状态', '状态', [1 => '启用', 0 => '禁用'])
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 添加渠道账号
     */
    public function channelAdd(){
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增')  //设置页面标题
            ->setPostUrl(url('channelsavepost')) //设置表单提交地址
            ->addFormItem('realname', 'text', '名称', '名称', [], 'required')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('password', 'password', '密码', '6-20位', [], 'required')
            ->addFormItem('mobile', 'text', '手机', '手机')
            ->addFormItem('service_rate', 'number', '渠道服务费比例', '请填写0-100之间');

        return $builder->show();
    }

    /**
     * 保存数据
     * @param $request
     * @param string $url
     * @param array $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Author  Doogie<461960962@qq.com>
     */
    public function channelSavePost($request, $url='', $data=[]){
        $post_data = input('post.');
        $post_data['group_id'] = \app\tenant\service\Auth::getChannelGroup('id');
        $post_data['pid'] = $this->tenantInfo('id');
        $post_data['leader_id'] = $this->tenantInfo('leader_id') ?: $this->tenantInfo('id');
        if(!empty($post_data['password'])){
            $post_data['password'] = fa_generate_pwd($post_data['password']);
        }

        $extend = [
            'service_rate' => $post_data['service_rate']
        ];
        unset($post_data['service_rate']);
        $res = $this->validate($post_data, "Tenant.edit");
        if($res !== true){
            return $this->error($res, '', ['token' => token()]);
        }

        try {
            if(empty($post_data[$this->pk])){
                $res = $this->tenantM->create($post_data);
            }else {
                $res = $this->tenantM->update($post_data);
            }
            if($res){
                if($tenant_info = TenantInfo::find($res['id'])){
                    $tenant_info->service_rate = $extend['service_rate'];
                    $tenant_info->save();
                }else{
                    TenantInfo::create(['id' => $res['id'], 'service_rate' => $extend['service_rate']]);
                }
                return $this->success("操作成功!", '');
            }else{
                return $this->error("未修改数据无需提交", null, ['token' => token()]);
            }
        }catch (\Exception $e){
            $msg = $e->getMessage();
            return $this->error($msg, null, ['token' => token()]);
        }
    }

    /**
     * 业绩明细
     * @return mixed|\support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function orders(){
        $begin_time_init = date('Y-m-d', strtotime('-7 days', time()));
        $end_time_init = date('Y-m-d');

        session('channel_begin_date') && $begin_time_init = session('channel_begin_date');
        session('channel_end_date') && $end_time_init = session('channel_end_date');

        $rid = input('rid', input('post.rid'));
        if(request()->isPost()){
            $order_field = 'order_time';
            $rids = TenantService::getRids();
            $post_data = input('post.');
            $where = [
                ['order.leader_id','=',TenantService::getLeaderId()]
            ];
            if(TenantService::isLeader()){
                $where[] = ['order.rid', '>', 0];
            }else{
                $where[] = ['order.rid', 'in', $rids];
            }
            if($rid){
                $where[] = ['order.rid', '=', in_array($rid, $rids) ? $rid : (TenantService::isLeader() ? $rid : -1)];
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
                $begin_time_init = $time_range[0];
                $end_time_init = $time_range[1];
                //$where[] = ['order_time', 'between', [, date('Y-m-d', strtotime()+86400)]];
            }else{
                if(empty($post_data['finish_time'])){
                    //$where[] = ['order_time', 'between', [$begin_time_init, $end_time_init]];
                }
            }
            if(!empty($post_data['finish_time'])){
                $time_range = explode('~', str_replace(' ','', $post_data['finish_time']));
                $begin_time_init = $time_range[0];
                $end_time_init = $time_range[1];
                //$where[] = ['finish_time', 'between', [$time_range[0], date('Y-m-d', strtotime($time_range[1])+86400)]];
                $order_field = 'finish_time';
            }
            if(!empty($time_range) && strtotime($time_range[1])-strtotime($time_range[0]) > 31 * 86400){
                return $this->error('时间范围不能超过31天');
            }
            $where[] = [$order_field, 'between', [$begin_time_init, $end_time_init . ' 23:59:59']];

            $query = $this->orderM->alias('order')
                ->where($where)
                ->join('cp_activity activity','activity.id=order.activity_id', 'left')
                ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
                ->join('channel','channel.rid = order.rid', 'left')
                ->join('tenant tenant','tenant.id = channel.tenant_id', 'left');
            $total = $query->count();
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order([$order_field => 'desc'])
                    ->field(['order.final_rate','order.commission_rate', 'order.valid_code','order.sku_name','order.price',
                        'order.sku_owner','order.order_id','order.plus','order.trace_type', 'order.sku_img_url', 'order.activity_id',
                        'order.order_time','order.finish_time','order.pay_month',  'order.union_tag', 'order.sku_id',
                        'order.sku_num', 'order.sku_frozen_num',  'order.sku_return_num','order.sku_shop_name',
                        'order.estimate_cos_price','order.estimate_fee','order.actual_cos_price',  'order.actual_fee',
                        'tenant.realname','activity.title as title','order.rid'
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
                'total_row' => $total_row, 'total_row_url' => url('ordersTotalPost'),
                'total_row_where' => $where
            ]);
        }

        $time_range_init = $begin_time_init.' ~ '. $end_time_init;
        $searches = [
            ['type' => 'select', 'name' => 'status', 'title' => '订单状态', 'options' => [-1 => '全部'] + Jd::orderStatus()],
            ['type' => 'complex', 'name' => 'complex', 'title' => '时间范围', 'value' => $time_range_init, 'defaultSelected' => 'order_time', 'options' => [
                ['type' => 'date','name' => 'order_time','elemName' => '下单时间', 'config' => ['type' => 'date', 'value' => $time_range_init]],
                ['type' => 'date','name' => 'finish_time','elemName' => '完成时间', 'config' => ['type' => 'date']]]
            ],
            ['type' => 'text', 'name' => 'order_id', 'title' => '订单号','placeholder' => '填写订单号'],
            ['type' => 'text', 'name' => 'activity_id', 'title' => '活动Id','placeholder' => '填写活动Id'],
            ['type' => 'text', 'name' => 'sku_id', 'title' => 'skuId','placeholder' => '商品skuId'],
            ['type' => 'text', 'name' => 'rid', 'title' => 'rid', 'value' => $rid]
        ];

        $builder = new ListBuilder();
        $builder->setSearch($searches)
            ->addTableColumn(['title' => 'ID', 'field' => 'act_info', 'minWidth' => 180])
            ->addTableColumn(['title' => '商品图片', 'field' => 'sku_img_url', 'type' =>'picture','minWidth' => 60])
            ->addTableColumn(['title' => '商品信息', 'field' => 'sku_info', 'minWidth' => 260])
            ->addTableColumn(['title' => '订单号', 'field' => 'order_id', 'minWidth' => 130])
            ->addTableColumn(['title' => '订单状态', 'field' => 'status', 'minWidth' => 70])
            ->addTableColumn(['title' => '时间', 'field' => 'time_info', 'minWidth' => 230])
            ->addTableColumn(['title' => '商品价格', 'field' => 'estimate_cos_price', 'minWidth' => 80])
            ->addTableColumn(['title' => '预估服务费', 'field' => 'estimate_fee', 'minWidth' => 80])
            ->addTableColumn(['title' => '服务费率', 'field' => 'commission_rate', 'minWidth' => 80])
            //->addTableColumn(['title' => '分成比例', 'field' => 'final_rate', 'minWidth' => 80])
            //->addTableColumn(['title' => '实际记佣金额', 'field' => 'actual_cos_price', 'minWidth' => 80])
            ->addTableColumn(['title' => '实际服务费', 'field' => 'actual_fee', 'minWidth' => 80])
            ->addTableColumn(['title' => '订单类型', 'field' => 'union_tag', 'minWidth' => 80])
            ->addTableColumn(['title' => 'rid', 'field' => 'rid'])
            ->addTableColumn(['title' => '渠道', 'field' => 'realname'])
            ->setCss(".jd-label{display: inline-block;height: 16px;line-height: 16px;font-size: 12px;background: #333;color: #f5a623;border-radius: 4px;padding: 0 3px;margin-left: 2px;}");

        return $builder->show(['total_row' => true]);
    }

    /**
     * 订单明细数据统计
     * @return \support\Response
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function ordersTotalPost(){
        $where = input('where');
        $query = $this->orderM->alias('order')
            ->where($where)
            ->join('cp_activity activity','activity.id=order.activity_id', 'left')
            ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'left')
            ->join('channel','channel.rid = order.rid', 'left')
            ->join('tenant tenant','tenant.id = channel.tenant_id', 'left');
        $total_row = [
            'sku_info' => '<div class="layui-row"><div class="layui-col-xs3">有效订单：' . $query->where('valid_code', 'in', [16, 17])->count('order.id') . '</div>',
            //'order_id' => '<div class="layui-col-xs3">预估计佣金额：￥' . $query->sum('order.estimate_cos_price') . '</div>',
            'time_info' => '<div class="layui-col-xs3">预估服务费：￥' . $query->sum('order.estimate_fee') . '</div></div>'
        ];
        return $this->success('', '', ['total_row_text' => implode("", $total_row)]);
    }

    /**
     * 列表
     * @throws \think\db\exception\DbException
     * @throws \Exception
     */
    public function index(){
        $leader_id = TenantService::getLeaderId($this->tenantInfo());
        $date_begin_init = date('Y-m-d');
        $date_end_init = date('Y-m-d');

        if(request()->isPost()){
            $post_data = input('post.');
            if(!empty($post_data['time_range'])){
                $time_range = explode('~', $post_data['time_range']);
                $date_begin_init = trim($time_range[0]);
                $date_end_init = trim($time_range[1]);
            }
            session(['channel_begin_date' => $date_begin_init]);
            session(['channel_end_date' => $date_end_init]);
            $where = [
                'leader_id' => $leader_id,
                'page_no' => $post_data['page'],
                'page_size' => $post_data['limit'],
                'begin_date' => $date_begin_init,
                'end_date' => $date_end_init
            ];

            $total = 0;
            $list = [];
            if(TenantService::isLeader()){
                !empty($post_data['channel_id']) && $where['id'] = $post_data['channel_id'];
                !empty($post_data['channel_name']) && $where['name'] = $post_data['channel_name'];
                $res = channelService::queryCpChannel($where);

                if($res['code']) {
                    $total = $res['totalNum'];
                    $list = $res['result'];
                }else{
                    return $this->error($res['errmsg']);
                }
            }else{
                if(count($rids = TenantService::getRids())){
                    foreach ($rids as $rid){
                        $where['id'] = $rid;
                        $res = channelService::queryCpChannel($where);
                        if($res['code']){
                            $total += $res['totalNum'];
                            $list += $res['result'];
                        }else{
                            return $this->error($res['errmsg']);
                        }
                    }
                }
            }

            if($total){
                $tenant_arr = $this->channelM->alias('channel')
                    ->join('tenant tenant','channel.tenant_id=tenant.id')
                    ->where('tenant.group_id', \app\tenant\service\Auth::getChannelGroup('id'))
                    ->where('channel.leader_id', $leader_id)
                    ->column('realname', 'channel.rid');
            }else{
                $tenant_arr = [];
                $list = [];
            }
            if($total){
                foreach ($list as $k => $v){
                    $v['realname'] = $tenant_arr[$v['id']] ?? '';
                    $v['channel_info'] = 'rid：' . $v['id'] . '<br>渠道名称：' . $v['name'].($v['defaultFlag'] ? '<br><span style="color: red;">【默认】</span>' :'');
                    $v['estimate_info'] = '有效引入订单量：' . $v['forecastOrderCnt'] . '<br>有效引入订单金额：' . $v['forecastGmv']. '<br>预估服务费：' . $v['forecastCommission'];
                    $v['actual_info'] = '完成订单量：' . $v['actualOrderCnt'] . '<br>完成订单金额：' . $v['actualGmv']. '<br>实际服务费：' . $v['actualCommission'];
                    $list[$k] = $v;
                }
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }
        $searches = [
            ['type' => 'date_range', 'name' => 'time_range', 'title' => '效果日期', 'value' => $date_begin_init .' ~ ' . $date_end_init]
        ];
        if(TenantService::isLeader()){
            $searches[]  = ['type' => 'complex', 'name' => 'complex', 'title' => '复合查询', 'options' => [
                ['type' => 'input','name' => 'channel_id','elemName' => '渠道ID'],
                ['type' => 'input','name' => 'channel_name','elemName' => '渠道名称']
            ]];
        }
        $builder = new ListBuilder();
        $builder->setSearch($searches)
            ->setTip('<a href="https://union.jd.com/searchResultDetail?articleId=109356" target="_blank">使用教程</a>')
            ->addTopButton('addnew')
            ->addTableColumn(['title' => '序号', 'type' => 'index', 'minWidth' => 70])
            ->addTableColumn(['title' => '渠道信息', 'field' => 'channel_info', 'minWidth' => 260])
            ->addTableColumn(['title' => '预估效果数据', 'field' => 'estimate_info', 'minWidth' => 200])
            ->addTableColumn(['title' => '实际效果数据', 'field' => 'actual_info', 'minWidth' => 200])
            ->addTableColumn(['title' => '渠道账号', 'field' => 'realname', 'minWidth' => 80])
            ->addTableColumn(['title' => '操作', 'minWidth' => 220, 'type' => 'toolbar'])
            ->addRightButton('edit', ['title' => '业绩明细', 'class' => 'layui-btn layui-btn-xs','href' => url('orders', ['rid' => '__data_id__'])])
            ->addRightButton('edit', ['title' => '关联账号', 'href' => url('edit', ['id' => '__data_id__', 'name' => '__data_name__'])])
            ->addRightButton('self', ['title' => '设为默认', 'data-ajax' => 1,'data-confirm' => 1,'class' => 'layui-btn layui-btn-xs layui-btn-warm','href' => url('setdefaultpost', ['id' => '__data_id__'])])
            ->addRightButton('self', ['title' => '删除', 'data-ajax' => 1,'data-confirm' => 1,'confirm' => '渠道删除后，在列表中将不再展示，24小时后历史链接中的渠道ID也会失效, 是否继续?','class' => 'layui-btn layui-btn-xs layui-btn-danger','href' => url('delpost', ['id' => '__data_id__'])]);

        return $builder->show();
    }

    /**
     * 删除
     * @throws \think\db\exception\DbException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function delPost()
    {
        if(request()->isPost()){
            if(! $id = input('id', 0)){
                return $this->error('非法请求');
            }
            $res = channelService::delCpChannel([
                'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
                'id' => $id
            ]);
            if($res['code']){
                return $this->success('删除成功！');
            }else{
                return $this->error($res['errmsg']);
            }
        }else{
            return $this->error('非法请求');
        }
    }

    /**
     * 设置默认
     * @throws \think\db\exception\DbException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function setDefaultPost()
    {
        if(request()->isPost()){
            if(! $id = input('id', 0)){
                return $this->error('非法请求');
            }
            $res = channelService::setCpChannelDefault([
                'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
                'id' => $id
            ]);
            if($res['code']){
                return $this->success('操作成功！');
            }else{
                return $this->error($res['errmsg']);
            }
        }else{
            return $this->error('非法请求');
        }
    }

    /**
     * 添加
     * @throws \think\db\exception\DbException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function add()
    {
        if(request()->isPost()){
            $post_data = input('post.');
            if(! $name = $post_data['name']){
                return $this->error('请填写渠道名称');
            }
            $res = channelService::saveCpChannel([
                'leader_id' => TenantService::getLeaderId($this->tenantInfo()),
                'name' => $name
            ]);
            if($res['code']){
                return $this->success('操作成功！');
            }else{
                return $this->error($res['errmsg']);
            }
        }
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增渠道')  //设置页面标题
            ->setPostUrl(url('add')) //设置表单提交地址
            ->setTip('渠道名称创建后，不可修改')
            ->addFormItem('name', 'text', '渠道名称', '支持数字&汉字&字符，不超过40个字符', [], 'required minlength="1" maxlength="40"');
        return $builder->show();
    }

    /**
     * 设置渠道账号
     * @return mixed|\support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function edit()
    {
        $leader_id = TenantService::getLeaderId($this->tenantInfo());
        if(request()->isPost()){
            $post_data = input('post.');
            if(empty($post_data[$this->pk])){
                $post_data['leader_id'] = $leader_id;
                $res = $this->channelM->create($post_data);
            }else {
                $res = $this->channelM->update($post_data);
            }
            if($res){
                return $this->success("操作成功!", '');
            }
            return $this->error("保存失败!", '', ['token' => token()]);
        }
        if(!$rid = input('id', 0)){
            return $this->error('参数错误');
        }

        $name = input('name', '');
        $data = [
            'rid' => $rid
        ];
        if(!empty($channel = $this->channelM->alias('channel')
            ->join('tenant tenant', 'channel.tenant_id=tenant.id')
            ->where('rid', $rid)
            ->field('channel.*')
            ->find())){
            $data['tenant_id'] = $channel['tenant_id'];
            $data['id'] = $channel['id'];
        }
        $tenant_arr = $this->tenantM->where('group_id', \app\tenant\service\Auth::getChannelGroup('id'))
            ->where('leader_id', $leader_id)
            ->column('realname', 'id');

        $builder = new FormBuilder();
        $builder->setMetaTitle('设置渠道账号')  //设置页面标题
            ->setPostUrl(url('edit')) //设置表单提交地址
            ->setTip('关联渠道账号(rid:'.$rid.',名称：'.$name.')')
            ->addFormItem('rid', 'hidden', 'rid', 'rid')
            ->addFormItem('tenant_id', 'chosen', '选择渠道', '选择渠道', $tenant_arr, 'required ')
            ->setFormData($data);
        if(!empty($channel['id'])){
            $builder->addFormItem('id', 'hidden', 'id', 'id');
        }
        return $builder->show();
    }
}