<?php
/**
 * Created by PhpStorm.
 * Script Name: ComponentModifyWxaServerDomain.php
 * Create: 2023/02/07 09:24
 * Description: 设置第三方平台服务器域名
 * link: https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/domain/modify_server_domain.html
 * Author: fdj<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent\Request\Mp;

class ComponentModifyWxaServerDomain implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/cgi-bin/component/modify_wxa_server_domain";
    private $getParams = array();
    private $postParams = array();
    private $action = 'get';
    private $isModifyPublishedTogether = false;
    private $wxaServerDomain = '';

    /**
     * Author: fudaoji<fdj@kuryun.cn>
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setAction($action)
    {
        $this->postParams['action'] = $action;
        $this->action = $action;
    }

    /**
     * Author: fudaoji<fdj@kuryun.cn>
     * @return bool
     */
    public function getIsModifyPublishedTogether()
    {
        return $this->isModifyPublishedTogether;
    }

    /**
     * @param bool $isModifyPublishedTogether
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setIsModifyPublishedTogether($isModifyPublishedTogether = false)
    {
        $this->postParams['is_modify_published_together'] = $isModifyPublishedTogether;
        $this->isModifyPublishedTogether = $isModifyPublishedTogether;
    }

    /**
     * Author: fudaoji<fdj@kuryun.cn>
     * @return string
     */
    public function getWxaServerDomain()
    {
        return $this->wxaServerDomain;
    }

    /**
     * @param string $wxaServerDomain
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setWxaServerDomain($wxaServerDomain)
    {
        $this->postParams['wxa_server_domain'] = $wxaServerDomain;
        $this->wxaServerDomain = $wxaServerDomain;
    }

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