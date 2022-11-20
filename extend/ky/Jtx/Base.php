<?php
/**
 * Created by PhpStorm.
 * Script Name: Base.php
 * Create: 12/20/21 11:33 PM
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace ky\Jtx;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use ky\Logger;

Abstract class Base
{
    private $options = [];
    protected $errMsg = '';
    protected $appKey = '';
    protected $baseUri = '';

    /**
     * @var Client
     */
    private $client;

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, $options);
        !empty($options['base_uri']) && $this->baseUri = $options['base_uri'];
        if(!empty($this->options['app_key'])){
            $this->appKey = $this->options['app_key'];
        }
    }

    public function setAppKey($app_key = ''){
        $this->appKey = $app_key;
        return $this;
    }

    public function setBaseUri($url = ''){
        $this->baseUri = $url;
        return $this;
    }

    protected function request($params = []){
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'timeout' => empty($this->options['timeout']) ? 0 : $this->options['timeout']
        ]);
        $url = empty($params['url']) ? '/' : $params['url'];
        $method = empty($params['method']) ? 'post' : $params['method'];
        $extra = [
            'http_errors' => false
        ];
        $headers = [
            'Content-Type'     => 'application/json;charset=UTF-8',
        ];
        if(!empty($params['headers'])){
            $headers = array_merge($headers, $params['headers']);
        }
        $extra['headers'] = $headers;
        if(!empty($params['data'])){
            if(isset($params['content_type']) && $params['content_type'] === 'form_params'){
                $extra['form_params'] = $params['data'];
                $extra['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
            }else{
                switch ($method){
                    case 'get':
                        $url .= '?' . http_build_query($params['data']);
                        break;
                    default:
                        $extra['json'] = $params['data'];
                        break;
                }
            }
        }

        //var_dump($method, $url, $extra);
        $response = $this->client->request($method, $url, $extra);

        if($response->getStatusCode() !== 200){
            $this->setError($response->getStatusCode());
            Logger::error($response->getStatusCode() .':'. $response->getBody()->getContents());
            return ['code' => 0, '接口路径与请求方式错误'];
        }
        if(! $res = json_decode($response->getBody()->getContents(), true)){
            Logger::error($response->getStatusCode() .':'. $response->getBody()->getContents());
            return ['code' => 0, '请求成功，但是结果为空'];
        }
        return $this->dealRes($res);
    }

    public function setError($code = 200){
        $list = [
            401 => '获取token失败',
            404 => '接口路径与请求方式错误',
            429 => '接口请求频率超过限制',
            500 => '服务端错误'
        ];
        $this->errMsg = isset($list[$code]) ? ($code . ':' .$list[$code]) : ($code.':未知错误');
    }

    public function getError(){
        return $this->errMsg;
    }

    abstract public function dealRes($res);

    /**
     * 接口暂未开放
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    protected function apiUnSupport(){
        return ['code' => 0, 'errmsg' => '此接口暂不支持'];
    }
}