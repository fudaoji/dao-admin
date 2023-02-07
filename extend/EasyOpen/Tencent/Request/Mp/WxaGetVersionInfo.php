<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaGetVersionInfo.php
 * Create: 2022/6/26 14:16
 * Description: 获取线上版本小程序信息
 * @link https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/get_versioninfo.html
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class WxaGetVersionInfo implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/getversioninfo";
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