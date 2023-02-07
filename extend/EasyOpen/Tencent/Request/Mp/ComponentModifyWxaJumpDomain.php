<?php
/**
 * Created by PhpStorm.
 * Script Name: ComponentModifyWxaJumpDomain.php
 * Create: 2023/02/07 09:24
 * Description: 设置第三方平台业务域名
 * link: https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/domain/modify_jump_domain.html
 * Author: fdj<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class ComponentModifyWxaJumpDomain implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/cgi-bin/component/modify_wxa_jump_domain";
    private $getParams = array();
    private $postParams = array();
    private $action = 'get';
    private $isModifyPublishedTogether = false;
    private $wxaJumpH5Domain = '';

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
    public function getWxaJumpH5Domain()
    {
        return $this->wxaJumpH5Domain;
    }

    /**
     * @param string $wxaJumpH5Domain
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setWxaJumpH5Domain($wxaJumpH5Domain)
    {
        $this->postParams['wxa_jump_h5_domain'] = $wxaJumpH5Domain;
        $this->wxaJumpH5Domain = $wxaJumpH5Domain;
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