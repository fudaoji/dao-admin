<?php
/**
 * Created by PhpStorm.
 * Script Name: WxOpenDeleteCategory.phpry.php
 * Create: 2018/8/30 14:07
 * Description: 删除类目
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class WxOpenDeleteCategory implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/cgi-bin/wxopen/deletecategory";
    private $first; //一级类目ID
    private $second; //二级类目ID
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
     * 设置first
     * @param string $first
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setFirst($first) {
        $this->first = $first;
        $this->postParams['first'] = $first;
    }

    /**
     * 获取first
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getFirst() {
        return $this->first;
    }

    /**
     * 设置second
     * @param string $second
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setSecond($second) {
        $this->second = $second;
        $this->postParams['second'] = $second;
    }

    /**
     * 获取second
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getSecond() {
        return $this->second;
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