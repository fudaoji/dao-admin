<?php
/**
 * Created by PhpStorm.
 * Script Name: Client.php
 * Create: 2022/10/19 18:00
 * Description: 公共客户端
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace ky\EasyCps;

abstract class Client
{
    protected $errMsg = '';

    protected $readTimeout;

    protected $connectTimeout;

    public function request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->readTimeout);
        }
        if ($this->connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        }
        //https 请求
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($ch, CURLOPT_POST, true);

        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->abort(0, curl_error($ch));
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                $this->abort($httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

    protected function abort($http_code = 0, $err = 'Error')
    {
        throw new \Exception($err, $http_code);
    }

    abstract public function dealRes($res);
}