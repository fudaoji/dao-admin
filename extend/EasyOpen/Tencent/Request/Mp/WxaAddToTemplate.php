<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaAddToTemplate.php
 * Create: 2018/8/30 14:12
 * Description: 将草稿添加到代码模板库
 * @link  https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/Mini_Programs/code_template/addtotemplate.html
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class WxaAddToTemplate implements RequestInterface
{
    private $url = "https://api.weixin.qq.com/wxa/addtotemplate";
    private $draftId;
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
     * 设置draft_id
     * @param string $draft_id
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setDraftId($draft_id) {
        $this->draftId = $draft_id;
        $this->postParams['draft_id'] = $draft_id;
    }

    /**
     * 获取$draft_id
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getDraftId() {
        return $this->draftId;
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