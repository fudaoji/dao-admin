<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaSpeedupAudit.php
 * Create: 2020/9/19 下午10:22
 * Description: 加速审核
 * @link https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/Mini_Programs/code/speedup_audit.html
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent\Request\Mp;

class WxaSpeedupAudit implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/speedupaudit";
    private $auditId;
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
     * 设置auditId
     * @param string $auditId
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setAuditId($auditId) {
        $this->auditId = $auditId;
        $this->postParams['auditid'] = $auditId;
    }

    /**
     * 获取auditId
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getAuditId() {
        return $this->auditId;
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