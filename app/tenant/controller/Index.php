<?php

namespace app\tenant\controller;

use app\TenantController;
use support\Response;
use Support\Request;;
use app\common\service\TenantApp as TaService;
use app\common\service\Tenant as TenantService;
use app\common\service\OrderApp as OrderAppService;
use app\common\service\Notice as NoticeService;

class Index extends TenantController
{
    public function index()
    {
        return $this->show();
    }

    public function welcome(){
        $notice_list = NoticeService::listTenantNewsNotices();
        $assign = [
            //'department' => TenantService::getDepartment(),
            'notice_list' => $notice_list,
            'app_active' => TaService::getActiveAppsNum(),
            'app_deadline' => TaService::getDeadlineAppsNum(),
            'staff_num' => count(TenantService::getTeamIds('id')),
            'order_app_num' => OrderAppService::getCompanyOrderNum()
        ];
        return $this->show($assign);
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
            'title' => '首页',
            'href'  => '/'.request()->app.'/index/welcome',
        ];
        $logoInfo = [
            'title' => dao_config('system.site.project_title'),
            'href' => '/'.request()->app.'/index/index'
        ];
        isset(dao_config('system.site')['backend_logo']) && $logoInfo['image'] = dao_config('system.site')['backend_logo'];
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
     * @param Request $request
     * @return Response
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function logout(Request $request)
    {
        $request->session->flush();
        //session([SESSION_TENANT => null]);
        return $this->redirect(url('auth/login'));
    }
}
