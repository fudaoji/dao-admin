<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaModifyDomain.phpphp
 * Create: 2018/8/30 11:34
 * Description: 设置小程序服务器域名
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class WxaModifyDomain implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/modify_domain";
    private $action;  //add添加, delete删除, set覆盖, get获取。当参数是get时不需要填四个域名字段
    private $requestDomain = array();  //request合法域名，当action参数是get时不需要此字段
    private $wsRequestDomain = array();  //socket合法域名，当action参数是get时不需要此字段
    private $uploadDomain = array();  //uploadFile合法域名，当action参数是get时不需要此字段
    private $downloadDomain = array();  //downloadFile合法域名，当action参数是get时不需要此字段
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
     * @return string
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * 设置requestDomain
     * @param array $requestDomain
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setRequestDomain($requestDomain) {
        $this->requestDomain = $requestDomain;
        $this->postParams['requestdomain'] = $requestDomain;
    }

    /**
     * 获取requestDomain
     * @return array
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getRequestDomain() {
        return $this->requestDomain;
    }

    /**
     * 设置requestDomain
     * @param array $wsRequestDomain
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setWsRequestDomain($wsRequestDomain) {
        $this->wsRequestDomain = $wsRequestDomain;
        $this->postParams['wsrequestdomain'] = $wsRequestDomain;
    }

    /**
     * 获取requestDomain
     * @return array
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getWsRequestDomain() {
        return $this->wsRequestDomain;
    }

    /**
     * 设置uploadDomain
     * @param array $uploadDomain
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setUploadDomain($uploadDomain) {
        $this->uploadDomain = $uploadDomain;
        $this->postParams['uploaddomain'] = $uploadDomain;
    }

    /**
     * 获取uploadDomain
     * @return array
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getUploadDomain() {
        return $this->uploadDomain;
    }

    /**
     * 设置downloadDomain
     * @param array $downloadDomain
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setDownloadDomain($downloadDomain) {
        $this->downloadDomain = $downloadDomain;
        $this->postParams['downloaddomain'] = $downloadDomain;
    }

    /**
     * 获取uploadDomain
     * @return array
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getDownloadDomain() {
        return $this->downloadDomain;
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
