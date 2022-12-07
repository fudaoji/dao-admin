<?php

namespace app\tenant\controller;

use app\common\model\CpActivity;
use app\common\model\CpGoods;
use app\common\model\CpOrder;
use app\common\model\TenantGroup;
use app\common\model\TenantRule;
use app\common\service\Tenant as TenantService;
use app\TenantController;
use support\Response;

class Index extends TenantController
{
    public function index()
    {
        return $this->show();
    }

    public function welcome(){
        $begin_time_cur = date('Ym01');
        $end_time_cur = date('Ymd 23:59:59', time());
        $begin_time_last = date('Ymd', strtotime('-1 month', strtotime($begin_time_cur)));
        $end_time_last = date('Ymd 23:59:59', strtotime($begin_time_cur) - 1);

        $tenant_info = $this->tenantInfo();
        $leader_id = TenantService::getLeaderId();
        $service_where = [
            ['order.leader_id','=', $leader_id],
        ];
        $activity_where = [
            ['leader_id','=', $leader_id],
        ];
        $goods_where = [
            ['leader_id','=', $leader_id],
        ];
        if(!TenantService::isLeader($tenant_info)){
            $service_where[] = $this->tenantWhere('goods', $tenant_info);
            $activity_where[] = ['tenant_id', '=', $tenant_info['id']];
            $goods_where[] = $this->tenantWhere('', $tenant_info);
        }

        $summary = [
            'activity' => [
                'cur_month' => CpActivity::where($activity_where)
                    ->where('create_time', 'between', [strtotime($begin_time_cur), strtotime($end_time_cur)])
                    ->count(),
                'last_month' => max(1, CpActivity::where($activity_where)
                    ->where('create_time', 'between', [strtotime($begin_time_last), strtotime($end_time_last)])
                    ->count())
            ],
            'goods' => [
                'cur_month' => CpGoods::where($goods_where)
                    ->where('create_time', 'between', [strtotime($begin_time_cur), strtotime($end_time_cur)])
                    ->count(),
                'last_month' => max(1, CpGoods::where($goods_where)
                    ->where('create_time', 'between', [strtotime($begin_time_last), strtotime($end_time_last)])
                    ->count())
            ],
            'service_djs' => [
                'cur_month' => CpOrder::alias('order')
                    ->where($service_where)
                    ->where([['valid_code','=', 16], ['modify_time', 'between', [date('Y-m-d', strtotime($begin_time_cur)), date('Y-m-d 23:59:59')]]])
                    ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'LEFT')
                    ->join('tenant tenant','tenant.id=goods.tenant_id', 'LEFT')
                    ->sum('estimate_fee'),
                'last_month' => max(1,CpOrder::alias('order')
                    ->where($service_where)
                    ->where([['valid_code','=', 16], ['modify_time', 'between', [date('Y-m-d', strtotime($begin_time_last)), date('Y-m-d H:i:s', strtotime($end_time_last))]]])
                    ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'LEFT')
                    ->join('tenant tenant','tenant.id=goods.tenant_id', 'LEFT')
                    ->sum('estimate_fee'))
            ],
            'service_yjs' => [
                'cur_month' => CpOrder::alias('order')
                    ->where($service_where)
                    ->where([['pay_month', 'between', [$begin_time_cur, $end_time_cur]]])
                    ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'LEFT')
                    ->join('tenant tenant','tenant.id=goods.tenant_id', 'left')
                    ->sum('actual_fee'),
                'last_month' => max(1, CpOrder::alias('order')
                    ->where($service_where)
                    ->where([['pay_month', 'between', [$begin_time_last, $end_time_last]]])
                    ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'LEFT')
                    ->join('tenant tenant','tenant.id=goods.tenant_id', 'left')
                    ->sum('actual_fee'))
            ]
        ];
        $assign = [
            'summary' => $summary
        ];
        return $this->show($assign);
    }

    /**
     * 数据统计
     * @return Response
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public function staticsPost(){
        $begin = strtotime(date('Ym01 00:00:00'));
        $end = strtotime("+1 month", $begin) - 86400;

        $x_data = [];
        $wjs_data = [];
        $yjs_data = [];
        $goods_data = [];

        $tenant_info = $this->tenantInfo();
        $leader_id = TenantService::getLeaderId();
        $service_where = [
            ['order.leader_id','=', $leader_id],
        ];
        $goods_where = [
            ['goods.leader_id','=', $leader_id],
        ];
        if(!TenantService::isLeader($tenant_info)){
            $service_where[] = $this->tenantWhere('goods', $tenant_info);
            $goods_where[] = $this->tenantWhere('goods', $tenant_info);
        }

        for($i = $begin; $i <= $end; $i+=86400){
            $x_data[] = date('d', $i);
            $wjs_data[] = CpOrder::alias('order')
                ->where($service_where)
                ->where([['valid_code','=', 16], ['modify_time', 'between', [date('Y-m-d', $i), date('Y-m-d 23:59:59', $i)]]])
                ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'LEFT')
                ->join('tenant tenant','tenant.id=goods.tenant_id', 'LEFT')
                ->sum('estimate_fee');
            //var_dump(CpOrder::alias('order')->getLastSql());
            $yjs_data[] = CpOrder::alias('order')
                ->where($service_where)
                ->where([
                    ['pay_month', 'between', [date('Ymd', $i), date('Ymd 23:59:59', $i)]]
                ])
                ->join('cp_goods goods','goods.sku_id=order.sku_id and goods.activity_id=order.activity_id', 'LEFT')
                ->join('tenant tenant','tenant.id=goods.tenant_id', 'LEFT')
                ->sum('actual_fee');

            $goods_data[] = [
                'date' => date('d', $i),
                '本月' => CpGoods::alias('goods')
                    ->where($goods_where)
                    ->where([
                        ['goods.create_time', 'between', [$i, $i + 86399]]
                    ])
                    ->count()
            ];
        }
        return $this->success('', '', ['xData' => $x_data, 'wjsData' => $wjs_data, 'yjsData' => $yjs_data, 'goodsData' => $goods_data]);
    }

    /**
     * 获取初始化数据
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function getSystemInit(){
        $homeInfo = [
            'title' => '数据统计',
            'href'  => '/'.request()->app.'/index/welcome',
        ];
        $logoInfo = [
            'title' => dao_config('system.site.project_title'),
            'href' => '/'.request()->app.'/index/index'
        ];
        isset(dao_config('system.site')['logo']) && $logoInfo['image'] = dao_config('system.site')['logo'];
        $menuInfo = \app\tenant\service\Auth::getMenuList($this->tenantInfo());
        $systemInit = [
            'homeInfo' => $homeInfo,
            'logoInfo' => $logoInfo,
            'menuInfo' => $menuInfo,
        ];
        return json($systemInit);
    }

    /**
     * 退出
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function logout()
    {
        session([SESSION_TENANT => null]);
        return $this->redirect(url('auth/login'));
    }
}
