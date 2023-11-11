<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaGetEffectiveServerDomain.php
 * Create: 2023/11/11 17:46
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent\Request\Mp;


class WxaGetEffectiveServerDomain implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/get_effective_domain";
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