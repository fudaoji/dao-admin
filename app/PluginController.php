<?php
/**
 * Created by PhpStorm.
 * Script Name: PluginController.php
 * Create: 2022/12/14 10:24
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app;

use app\common\service\App;

class PluginController extends TenantController
{

    /**
     * 统一视图
     * @param array $assign
     * @param string $view
     * @param null $app
     * @return mixed
     * @Author  fudaoji<fdj@kuryun.cn>
     */
    public function show($assign = [], $view = '', $app = ''){
        $controller_layer = explode('/', \request()->getController());
        switch (count($controller_layer)){
            case 6:
                $app = $controller_layer[4];
                break;
        }
        $controller = str_replace("controller", "", $controller_layer[count($controller_layer) - 1]);
        $action = \request()->getAction();
        if ($view) {
            $layer = explode('/', $view);
            switch(count($layer)){
                case 3:
                    $controller = $layer[1];
                    $action = $layer[2];
                    $app = $layer[0];
                    break;
                case 2:
                    $controller = $layer[0];
                    $action = $layer[1];
                    break;
                default:
                    $action = $layer[0];
                    break;
            }
        }
        $template = $app . DIRECTORY_SEPARATOR . $this->theme. DIRECTORY_SEPARATOR. $controller . DIRECTORY_SEPARATOR . $action;
        $assign['controller'] = $controller;
        $assign['action'] = $action;
        $assign['app'] = PLUGIN;
        return view($template, $assign, null);
    }
}