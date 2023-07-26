<?php

/**
 * Created by PhpStorm.
 * Script Name: Setting.php
 * Create: 2020/5/24 上午10:25
 * Description: 站点配置
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\controller;
use app\AdminController;
use app\common\constant\Common;
use app\common\model\Setting as SettingM;
use app\common\service\Payment;
use app\common\service\Upload;
use app\common\service\Sms;

class Setting extends AdminController
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
            'site' => [
                'title' => '平台设置',
                'href' => url('index', ['name' => 'site'])
            ],
            'upload' => [
                'title' => '附件设置',
                'href' => url('index', ['name' => 'upload'])
            ],
            'pay' => [
                'title' => '支付设置',
                'href' => url('index', ['name' => 'pay'])
            ],
            'sms' => [
                'title' => '短信设置',
                'href' => url('index', ['name' => 'sms'])
            ],
            'common' => [
                'title' => '其他设置',
                'href' => url('index', ['name' => 'common'])
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
        $current_name = input('name', 'site');
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
            case 'common':
                $builder->addFormItem('map_title', 'legend', '地图', '地图')
                    ->addFormItem('map_qq_key', 'text', '腾讯地图key', '获取方法详见：https://lbs.qq.com/', [], 'required maxlength=150');
                break;
            case 'pay':
                $builder->addFormItem('channel', 'select', '支付通道', '支付通道', Payment::channels(), 'required maxlength=150')
                    ->addFormItem('wx_title', 'legend', '微信支付(V3)', '微信支付(V3)')
                    ->addFormItem('wx_appid', 'text', 'AppId', 'AppId', [], 'required maxlength=150')
                    ->addFormItem('wx_mchid', 'text', '商户ID', '商户ID', [], 'required maxlength=100')
                    ->addFormItem('wx_p_appid', 'text', '服务商AppId', '如果是服务商模式，需要填写此项', [], ' maxlength=150')
                    ->addFormItem('wx_p_mchid', 'text', '服务商商户ID', '如果是服务商模式，需要填写此项', [], ' maxlength=100')
                    ->addFormItem('wx_key', 'text', '支付秘钥', '支付秘钥', [], 'required maxlength=32 minlength=32')
                    ->addFormItem('wx_cert_path', 'textarea', '支付证书cert', '请在微信商户后台下载支付证书，用记事本打开apiclient_cert.pem，并复制里面的内容粘贴到这里。', [], 'maxlength=20000')
                    ->addFormItem('wx_key_path', 'textarea', '支付证书key', '请在微信商户后台下载支付证书，使用记事本打开apiclient_key.pem，并复制里面的内容粘贴到这里。', [], ' maxlength=20000')
                    ->addFormItem('wx_rsa_path', 'textarea', 'RSA公钥', '企业付款到银行卡需要RSA公钥匙');
                break;
            case 'sms':
                $builder->addFormItem('channel', 'select', '短信商', '短信运营商', Sms::drivers(), 'required maxlength=150')
                    ->addFormItem('account', 'text', 'sms账号', 'sms账号', [], 'required maxlength=150')
                    ->addFormItem('pwd', 'text', 'sms密码', 'sms密码', [], 'required maxlength=150');
                break;
            case 'site':
                !isset($data['close']) && $data['close'] = 0;
                !isset($data['switch_reg']) && $data['switch_reg'] = 1;
                $builder->addFormItem('project_title', 'text', '平台名称', '平台名称')
                    ->addFormItem('logo', 'picture_url', '前台Logo', '尺寸400x400')
                    ->addFormItem('slogan', 'text', 'Slogan', '宣传标语',[], 'maxlength=200')
                    ->addFormItem('kefu', 'picture_url', '客服信息', '客服信息')
                    ->addFormItem('backend_logo', 'picture_url', '后台Logo', '尺寸400x400')
                    ->addFormItem('switch_reg', 'radio', '注册入口', '注册入口', Common::status())
                    ->addFormItem('tg_legend', 'legend', '推广', '推广')
                    ->addFormItem('seo_keywords', 'text', '关键词', '关键词',[], 'maxlength=200')
                    ->addFormItem('seo_description', 'textarea', '描述', '描述',[], 'maxlength=200')
                    ->addFormItem('beian', 'text', '备案码', '备案码',[], 'maxlength=200')
                    ->addFormItem('tongji', 'textarea', '统计代码', '统计代码');
                break;
            case 'upload':
                empty($data) && $data = [
                    'driver' => 'qiniu',
                    'upload_path' => './public/uploads/',
                    'file_size' => 53000000,
                    'image_size' => 5000000,
                    'image_ext' => 'jpg,gif,png,jpeg',
                    'file_ext' => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml,mp3,mp4,xls,xlsx,pdf',
                ];
                //$data['driver'] = 'qiniu';
                $builder->addFormItem('driver_title', 'legend', '上传驱动', '上传驱动')
                    ->addFormItem('driver', 'radio', '上传驱动', '上传驱动', Upload::locations())
                    ->addFormItem('local_title', 'legend', '本地上传', '本地上传')
                    ->addFormItem('upload_path', 'text', '上传目录', '请填写public下的目录，不用写public')
                    ->addFormItem('qiniu_title', 'legend', '七牛上传', '七牛上传')
                    ->addFormItem('qiniu_ak', 'text', '七牛accessKey', '七牛accessKey')
                    ->addFormItem('qiniu_sk', 'text', '七牛secretKey', '七牛secretKey')
                    ->addFormItem('qiniu_bucket', 'text', '七牛bucket', '七牛bucket')
                    ->addFormItem('qiniu_domain', 'url', '七牛domain', '七牛domain')
                    ->addFormItem('aliyun', 'legend', '阿里云oss参数', '阿里云oss参数')
                    ->addFormItem('aliyun_ak', 'text', 'AccessKeyId', 'AccessKeyId')
                    ->addFormItem('aliyun_sk', 'text', 'AccessKeySecret', 'AccessKeySecret')
                    ->addFormItem('aliyun_bucket', 'text', 'bucket', 'bucket')
                    ->addFormItem('aliyun_domain', 'url', '域名', '域名')
                    ->addFormItem('qcloud', 'legend', '腾讯云oss参数', '腾讯云oss参数')
                    ->addFormItem('qcloud_ak', 'text', 'secretId', 'secretId')
                    ->addFormItem('qcloud_sk', 'text', 'secretKey', 'secretKey')
                    ->addFormItem('qcloud_region', 'text', '区域region', '区域region')
                    ->addFormItem('qcloud_bucket', 'text', 'bucket', 'bucket')
                    ->addFormItem('qcloud_domain', 'url', '域名', '域名')
                    ->addFormItem('image_title', 'legend', '图片设置', '图片设置')
                    ->addFormItem('image_size', 'number', '图片大小限制', '单位B', [], 'required min=1 max=1000000000')
                    ->addFormItem('image_ext', 'text', '图片格式支持', '多个用逗号隔开', [], 'required')
                    ->addFormItem('file_title', 'legend', '文件设置', '文件设置')
                    ->addFormItem('file_size', 'number', '文件大小限制', '单位B', [], 'required min=1 max=1000000000')
                    ->addFormItem('file_ext', 'text', '文件格式支持', '多个用逗号隔开', [], 'required')
                    ->addFormItem('voice_title', 'legend', '音频设置', '音频设置')
                    ->addFormItem('voice_size', 'number', '音频大小限制', '单位B', [], 'required min=1 max=1000000000')
                    ->addFormItem('voice_ext', 'text', '音频格式支持', '多个用逗号隔开', [], 'required')
                    ->addFormItem('video_title', 'legend', '视频设置', '视频设置')
                    ->addFormItem('video_size', 'number', '视频大小限制', '单位B', [], 'required min=1 max=1000000000')
                    ->addFormItem('video_ext', 'text', '视频格式支持', '多个用逗号隔开', [], 'required');
                break;
        }
        $builder->setFormData($data)
            ->setTabNav($this->tabList(), $current_name);
        return $builder->show();
    }
}