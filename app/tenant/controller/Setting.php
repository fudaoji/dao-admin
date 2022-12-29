<?php

/**
 * Created by PhpStorm.
 * Script Name: Setting.php
 * Create: 2020/5/24 上午10:25
 * Description: 站点配置
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;
use app\common\model\TenantSetting as SettingM;
use app\common\service\Sms;
use app\TenantController;

class Setting extends TenantController
{
    /**
     * @var SettingM
     */
    protected $model;

    public function __construct()
    {
        parent::__construct(); // TODO: Change the autogenerated stub
        $this->model = new SettingM();
    }

    public function tabList(){
        return [
            'sms' => [
                'title' => '短信设置',
                'href' => url('index', ['name' => 'sms'])
            ]
        ];
    }

    /**
     * 设置
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function index(){
        $current_name = input('name', 'sms');
        $setting = $this->model->where([['name','=', $current_name]])
            ->find();
        if(request()->isPost()){
            $post_data = input('post.');
            unset($post_data['__token__']);
            if(empty($setting)){
                $res = $this->model->save([
                    'name' => $current_name,
                    'title' => $this->tabList()[$current_name]['title'],
                    'value' => json_encode($post_data, JSON_UNESCAPED_UNICODE)
                ]);
            }else{
                $res = $this->model->update([
                    'id' => $setting['id'],
                    'value' => json_encode($post_data, JSON_UNESCAPED_UNICODE)
                ]);
            }
            if($res){
                $this->model->settings(true);
                return $this->success('保存成功', url('index', ['name' => $current_name]));
            }else{
                return $this->error('保存失败，请刷新重试', '', ['token' => token()]);
            }
        }

        if(empty($setting)){
            $data = [];
        }else{
            $data = json_decode($setting['value'], true);
        }

        $builder = new FormBuilder();
        switch ($current_name){
            case 'sms':
                $builder->addFormItem('channel', 'select', '短信商', '短信运营商', Sms::drivers(), 'required maxlength=150')
                    ->addFormItem('account', 'text', 'sms账号', 'sms账号', [], 'required maxlength=150')
                    ->addFormItem('pwd', 'text', 'sms密码', 'sms密码', [], 'required maxlength=150');
                break;
        }
        $builder->setFormData($data)
            ->setTabNav($this->tabList(), $current_name);
        return $builder->show();
    }
}