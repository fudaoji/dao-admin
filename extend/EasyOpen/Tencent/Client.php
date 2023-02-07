<?php
/**
 * Created by PhpStorm.
 * Script Name: Client.php
 * Create: 2023/2/4 17:57
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent;

class Client
{
    public $appid;
    public $appSecretkey;
    public $connectTimeout;
    public $readTimeout;
    private $errMsg = '';


    /**
     * curl请求入口
     * @param string $url
     * @param string $postFields
     * @return object|boolean
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function curl($url, $postFields=null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if(stripos($url, "https://") != false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if(is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            $postMultipart = false;
            foreach($postFields as $k => $v) {
                if(is_int($k) && count($postFields) == 1){ //例如发布小程序就是这种情况
                    $postBodyString = '{}';
                    break;
                }
                if("@" != @substr($v, 0, 1)) { // 判断是不是文件上传
                    $postBodyString = json_encode($postFields, JSON_UNESCAPED_UNICODE);
                }else {
                    //文件上传用multipart/form-data, 否则用www-form-urlencode
                    $postMultipart = true;
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }else {
                //curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
                // 发送模版消息时，对转换为json后的数据，不能截取掉最后一个字符
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postBodyString);
            }
        }

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            $wxCode = curl_errno($ch);
            $wxMsg  = curl_error($ch);
            $this->setErrMsg('curl发生错误，code: ' . $wxCode . ' msg: ' . $wxMsg);
            return  false;
        }else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 != $httpStatusCode) {
                $this->setErrMsg('http返回错误，code: ' . $httpStatusCode . 'msg: ' . var_export($response, true));
                return  false;
            }
        }
        curl_close($ch);
        return $response;
    }

    /**
     * 执行请求
     * @param string $request
     * @param string $accessToken
     * @param bool $download
     * @return object|array
     * @throws \Exception
     * @author fudaoji<fdj@kuryun.cn>
     */
    public function execute($request, $accessToken = null, $download = false) {
        if(method_exists($request, 'check')) {
            $request->check();
        }

        //组装get参数
        $getParams = $request->getParams();
        $requestUrl = $request->getUrl() . "?";

        //拼装accessToken
        if($accessToken) {
            $requestUrl .= "access_token=" . $accessToken . "&";
        }

        foreach($getParams as $k => $v) {
            $requestUrl = $requestUrl . $k . "=" . $v . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);

        $postParams = $request->postParams();
        if(($resp = $this->curl($requestUrl, $postParams)) === false){
            return $this->error($resp);
        }

        if($download) {
            return $this->success($resp);
        }

        $respWellFormed = false;
        $respObject = json_decode($resp, true);

        if(null !== $respObject) {
            $respWellFormed = true;
            if(isset($respObject["errcode"]) && $respObject["errcode"] != 0) {
                return $this->error(ErrorMsg::getErrorMsg($respObject['errcode'], $respObject["errmsg"]), $respObject);
            }
        }
        if(false === $respWellFormed) {
            return $this->error('收消息: 收到的格式不是合法的json格式, resp: ' . var_export($respObject, true));
        }

        return $this->success($respObject);
    }

    private function setErrMsg(string $string = '')
    {
        $this->errMsg = $string;
    }

    private function error($msg = '', $data = [])
    {
        !empty($data['errmsg']) && $data['ori_errmsg'] = $data['errmsg'];
        $data['code'] = 0;
        $data['errmsg'] = $msg;
        return $data;
    }

    private function success($data = [], $msg = '')
    {
        return ['code' => 1, 'msg' => $msg, 'data' => $data];
    }
}