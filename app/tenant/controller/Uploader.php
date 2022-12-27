<?php

/**
 * Created by PhpStorm.
 * Script Name: Upload.php
 * Create: 2020/5/27 上午12:47
 * Description: 上传控制器
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace app\tenant\controller;

use app\common\model\Setting;
use app\common\service\Upload;
use app\TenantController;
use plugin\wechat\app\service\Setting as SettingService;

class Uploader extends TenantController
{
    /**
     * @var Upload
     */
    protected $uploadService;
    public function __construct(){
        parent::__construct();
        Setting::instance()->settings();
        $this->uploadService = new Upload(dao_config('system.upload'));
    }

    /**
     * 图片上传
     * Author: Doogie <461960962@qq.com>
     */
    public function picturePost()
    {
        $upload_config_pic = $this->uploadService->config();
        return $this->upload($upload_config_pic);
    }

    /**
     * 文件上传
     * Author: Doogie <461960962@qq.com>
     */
    public function filePost(){
        $upload_config_file = $this->uploadService->config('file');
        return $this->upload($upload_config_file);
    }

    /**
     * 最终的上传操作
     * @param array $config
     * @return mixed
     * @Author  Doogie<461960962@qq.com>
     * @throws \Exception
     */
    private function upload($config = []){
        /* 调用文件上传组件上传文件 */
        $return = $this->uploadService->upload(request()->file(), $config, ['uid' => $this->getPrefix()]);
        return json($return);
    }

    private function getPrefix(){
        return 'tenant_'.$this->tenantInfo('id');
    }

    /**
     * ueditor的服务端接口
     * @Author: Doogie <461960962@qq.com>
     */
    public function editorPost(){
        $action = input('action');
        $ue_config = $this->uploadService->ueConfig();

        switch ($action) {
            case 'config':
                $return = $ue_config;
                break;
            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $return = $this->uploadService->ueUpload(request()->file(), $action, ['from' => 2, 'uid' => $this->getPrefix()]);
                break;

            /* 列出图片 */
            case 'listimage':
                /* 列出文件 */
            case 'listfile':
                $return = $this->uploadService->ueList($action, ['uid' => $this->getPrefix()]);
                break;
            /* 抓取远程文件 */
            case 'catchimage':
                $return['state'] = '请求地址出错';
                break;
            default:
                $return['state'] = '请求地址出错';
                break;
        }

        return json($return);
    }

    /**
     * 上传文件到根目录
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function fileToRootPost(){
        if(request()->isPost()){
            try {
                // 获取表单上传文件 例如上传了001.jpg
                $file = request()->file('file');
                if(! $file->isValid()){
                    return $this->error("文件非法!: " . $file->getUploadErrorCode());
                }
                $file_path = '/'.$file->getUploadName();
                // 移动到服务器的上传目录 并且使用原文件名
                $file->move(public_path().$file_path);
                return $this->success('上传成功', '', ['src' => $file_path]);
            }catch (\Exception $e){
                return $this->error('文件上传错误!:' . $e->getMessage());
            }
        }
    }
}