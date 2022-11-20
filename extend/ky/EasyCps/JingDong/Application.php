<?php

namespace ky\EasyCps\JingDong;

use ky\EasyCps\Client;

class Application extends Client
{
    private static $instance;

    private $gatewayUrl = 'https://api.jd.com/routerjson';

    public $appKey;

    public $appSecret;

    public $format = "json";

    private $signMethod = "md5";

    private $v = "1.0";
    protected $request;

    public function __construct($appKey = "", $appSecret = "")
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
    }

    /**
     * 单例获取当前对象
     * @Author: YearDley
     * @Date: 2018/4/26
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 魔术方法 调用不存在的静态方法时触发
     * @Author: YearDley
     * @Date: 2018/4/26
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $obj = self::getInstance();
        return $obj->$name($arguments);
    }

    /**
     * 执行
     * @Author: YearDley
     * @Date: 2018/4/26
     * @param $request
     * @return mixed
     */
    public function execute($request, $access_token = null)
    {
        date_default_timezone_set('Asia/Shanghai');
        $timestamp = date('Y-m-d H:i:s');
        $this->request = $request;

        $params = [
            'v' => $this->v,
            'method' => $request->getMethod(),
            'app_key' => $this->appKey,
            'sign_method' => $this->signMethod,
            'format' => $this->format,
            'timestamp' => $timestamp,
        ];
        if (null != $access_token) {
            $params["access_token"] = $access_token;
        }

        $params['360buy_param_json'] = $request->getParamJson();;

        $params['sign'] = $this->generateSign($params);

        $requestUrl = $this->gatewayUrl . "?";
        foreach ($params as $k => $v) {
            $requestUrl .= "$k=" . urlencode($v) . '&';
        }

        $requestUrl = substr($requestUrl, 0, -1);

        $resp = $this->request($requestUrl);
        return $this->dealRes(json_decode($resp, true));
    }

    public function oauth()
    {
        return new Oauth();
    }


    /**
     * 生成加密签名
     * @param $params
     * @return string
     */
    public function generateSign($params)
    {
        ksort($params);
        $stringToBeSigned = $this->appSecret;
        foreach ($params as $k => $v) {
            if (!is_array($v) && "@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $this->appSecret;
        return strtoupper(md5($stringToBeSigned));
    }

    public function dealRes($res)
    {
        $return = ['code' => 0];
        if(!empty($res['error_response'])){
            $return['ori_code'] = $res['error_response']['code'];
            $return['errmsg'] = $this->setError($return['ori_code']);
        }else{
            $response = $res[str_replace('.', '_', $this->request->getMethod() . '_responce')];
            if($response['code'] == 0){
                $result_key = $this->request->getResultKey();
                $result = json_decode($response[$result_key], true);
                $return['ori_code'] = $result['code'];
                if($result['code'] != 200){
                    //var_dump($result);
                    $return['errmsg'] = $this->setError($return['ori_code']);
                }else{
                    $return['code'] = 1;
                    $return['data'] = $result;
                }
            }
        }
        return $return;
    }

    public function setError($code = -1){
        $list = [
            21 => 'appkey错误',
            400 => '参数错误',
            453 => '无效请求-查询时间范围超过30分钟',
            67 => '参数值错误'
        ];
        $this->errMsg = isset($list[$code]) ? ($code . ':' .$list[$code]) : ($code.':未知错误');
        return $this->errMsg;
    }
}
