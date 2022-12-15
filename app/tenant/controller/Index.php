<?php

namespace app\tenant\controller;

use app\TenantController;
use support\Response;

class Index extends TenantController
{
    public function index()
    {
        return $this->show();
    }

    public function welcome(){
        $assign = [

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
