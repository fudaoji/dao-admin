<?php
/**
 * Created by PhpStorm.
 * Script Name: ComponentGetPrivacySetting.php
 * Create: 2023/02/06 09:24
 * Description: 查询小程序用户隐私保护指引
 * link: https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/privacy_config/get_privacy_setting.html
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace EasyOpen\Tencent\Request\Mp;

class ComponentSetPrivacySetting
{
    private $url = "https://api.weixin.qq.com/cgi-bin/component/setprivacysetting";
    private $getParams = array();
    private $postParams = array('{}');
    private $ownerSetting = [];
    private $settingList = [];
    private $privacyVer = 2; //用户隐私保护指引的版本，1表示现网版本；2表示开发版。默认是2开发版。

    /**
     * 获取PrivacyVer
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function getPrivacyVer(){
        return $this->privacyVer;
    }

    /**
     * 设置PrivacyVer
     * @param string $ver
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function setPrivacyVer($ver) {
        $this->privacyVer = $ver;
        $this->postParams['privacy_ver'] = $ver;
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

    public function getOwnerSetting(){
        return $this->ownerSetting;
    }

    public function setOwnerSetting($owner_setting = []){
        $this->ownerSetting = $owner_setting;
        $this->postParams['owner_setting'] = $owner_setting;
    }
    public function getSettingList(){
        return $this->settingList;
    }

    public function setSettingList($setting_list = []){
        $this->settingList = $setting_list;
        $this->postParams['setting_list'] = $setting_list;
    }
}