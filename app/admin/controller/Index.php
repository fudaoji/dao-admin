<?php

namespace app\admin\controller;

use App\admin\model\AdminGroup;
use App\admin\model\AdminRule;
use app\AdminController;
use support\Response;

class Index extends AdminController
{
    public function index()
    {
        return $this->show();
    }

    public function welcome(){
        return $this->show();
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
            'href'  => '/admin/index/welcome',
        ];
        $logoInfo = [
            'title' => dao_config('system.site.project_title'),
            'href' => '/admin/index/index',
            'image' => '/static/imgs/admin/logo-167x167.png',
        ];
        isset(dao_config('system.site')['logo']) && $logoInfo['image'] = dao_config('system.site.logo');
        $menuInfo = \App\admin\service\Auth::getMenuList($this->adminInfo());
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
        session([SESSION_ADMIN => null]);
        return $this->success('安全退出', url('auth/login'));
    }
}
