<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaRelease.php
 * Create: 2023/2/4 14:16
 * Description: 发布已通过审核的小程序
 * @link https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/Mini_Programs/code/release.html
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class WxaRelease
{
    private $url = "https://api.weixin.qq.com/wxa/release";
    private $getParams = array();
    private $postParams = array('{}');

    /**
     * 获取请求url
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     * 设置请求地址
     * @param string $url
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * get请求参数
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getParams() {
        return $this->getParams;
    }

    /**
     * post请求参数
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function postParams() {
        return $this->postParams;
    }
}