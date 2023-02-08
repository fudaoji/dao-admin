<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaGetTemplateDraftList.php
 * Create: 2023/02/08 17:25
 * Description: 获取代码草稿列表
 * @link https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/Mini_Programs/code_template/gettemplatedraftlist.html
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent\Request\Mp;

class WxaGetTemplateDraftList implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/gettemplatedraftlist";
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