<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaGetAuditStatus.php
 * Create: 2023/02/03 19:44
 * Description:查询某个指定版本的审核状态
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class WxaGetAuditStatus implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/get_auditstatus";
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
