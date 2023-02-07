<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaSubmitAudit.php
 * Create: 2023/02/07 18:03
 * Description:将第三方提交的代码包提交审核
 * @link https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/Mini_Programs/code/submit_audit.html
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;


class WxaSubmitAudit
{
    private $url = "https://api.weixin.qq.com/wxa/submit_audit";
    private $itemList = array();
    private $getParams = array();
    private $postParams = array();
    private $versionDesc = '';
    private $privacyApiNotUse = true;

    /**
     * 设置itemList
     * @param bool $bool
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setPrivateApiNotUse($bool = true) {
        $this->privacyApiNotUse = $bool;
        $this->postParams['privacy_api_not_use'] = $bool;
    }

    /**
     * 获取privacy_api_not_use
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getPrivateApiNotUse() {
        return $this->privacyApiNotUse;
    }

    /**
     * @return string
     */
    public function getVersionDesc()
    {
        return $this->versionDesc;
    }

    /**
     * @param string $versionDesc
     */
    public function setVersionDesc($versionDesc)
    {
        $this->versionDesc = $versionDesc;
        $this->postParams['version_desc'] = $this->versionDesc;
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
     * 设置itemList
     * @param array $itemList
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setItemList($itemList) {
        $this->itemList = $itemList;
        $this->postParams['item_list'] = $itemList;
    }

    /**
     * 获取itemList
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getItemList() {
        return $this->itemList;
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