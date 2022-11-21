<?php
/**
 * Created by PhpStorm.
 * Script Name: Base.php
 * Create: 2022/7/7 11:23
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\install\controller;

use app\BaseController;

class Base extends BaseController
{
    protected array $assign = [];
    protected string $module = 'install';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 统一视图
     * @param array $assign
     * @param string $view
     * @param null $app
     * @return mixed
     * @Author  fudaoji<fdj@kuryun.cn>
     */
    public function show($assign = [], $view = '', $app = null){
        $assign['module'] = $this->module;
        $assign['controller'] = strtolower(request()->getController());
        $assign['action'] = strtolower(request()->getAction());
        $assign['company'] = '厦门酷云网络科技有限公司';
        $assign['official_web'] = 'https://kyphp.kuryun.com/';
        $assign['app_name'] = 'DaoAdmin';
        $assign['install_url'] = request()->domain();

        $this->assign = array_merge($this->assign, $assign);

        if (!$view) {
            $view = $assign['controller']. '/' . $assign['action'];
        }
        return view($view, $this->assign);
    }

}