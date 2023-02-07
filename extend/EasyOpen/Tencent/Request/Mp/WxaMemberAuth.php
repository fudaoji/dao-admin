<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaMemberAuth.php
 * Create: 2023/2/6 13:58
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent\Request\Mp;

class WxaMemberAuth implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/memberauth";
    private $getParams = array();
    private $postParams = array();
    private $action = 'get_experiencer';

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function getParams()
    {
        return $this->getParams;
    }

    /**
     * @inheritDoc
     */
    public function postParams()
    {
        return $this->postParams;
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
}