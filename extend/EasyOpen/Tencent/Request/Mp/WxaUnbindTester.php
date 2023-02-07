<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaUnbindTesterter.php
 * Create: 2018/8/30 14:14
 * Description: 解除绑定小程序的体验者
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class WxaUnbindTester implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/unbind_tester";
    private $wechatId;
    private $getParams = array();
    private $postParams = array();

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
     * 设置wechatId
     * @param string $wechatId
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setWechatId($wechatId) {
        $this->wechatId = $wechatId;
        $this->postParams['wechatid'] = $wechatId;
    }

    /**
     * 获取$wechatId
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getWechatId() {
        return $this->wechatId;
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