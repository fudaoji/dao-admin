<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaGetQrcode.php
 * Create: 2020/7/27 下午10:25
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent\Request\Mp;

class WxaGetQrcode implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/get_qrcode";
    private $page; //页面路径


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
     * @return mixed
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setPage($page)
    {
        $this->page = urlencode($page);
        $this->postParams['page'] = $this->page;
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