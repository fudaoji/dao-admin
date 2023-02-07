<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaSetWebViewDomain.php
 * Create: 2023/2/6 13:41
 * Description: 设置小程序业务域名（仅供第三方代小程序调用）
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class WxaSetWebViewDomain
{
    private $url = "https://api.weixin.qq.com/wxa/setwebviewdomain";
    private $action; //add添加, delete删除, set覆盖, get获取。当参数是get时不需要填webviewdomain字段。
    private $webViewDomain = array();  //小程序业务域名，当action参数是get时不需要此字段
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
     * 设置action
     * @param string $action
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setAction($action) {
        $this->action = $action;
        $this->postParams['action'] = $action;
    }

    /**
     * 获取action
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * 设置webViewDomain
     * @param string/array $webViewDomain
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setWebViewDomain($webViewDomain) {
        $this->webViewDomain = $webViewDomain;
        $this->postParams['webviewdomain'] = $webViewDomain;
    }

    /**
     * 获取webViewDomain
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getWebViewDomain() {
        return $this->webViewDomain;
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