<?php
/**
 * Created by PhpStorm.
 * Script Name: Payment.php
 * Create: 2023/3/16 14:58
 * Description: 支付服务
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace app\common\service;

use Yansongda\Pay\Pay;

class Payment
{
    const WECHAT = 'wechat';
    const ALIPAY = 'alipay';
    const UNIPAY = 'unipay';

    static function channels($id = null){
        $list = [
            self::WECHAT => '微信支付'
        ];
        return $list[$id] ?? $list;
    }

    //配置格式
    /**
     * @var array
     */
    private static $config = [
        'alipay' => [
            'default' => [
                // 必填-支付宝分配的 app_id
                'app_id' => '',
                // 必填-应用私钥 字符串或路径
                // 在 https://open.alipay.com/develop/manage 《应用详情->开发设置->接口加签方式》中设置
                'app_secret_cert' => '',
                // 必填-应用公钥证书 路径
                // 设置应用私钥后，即可下载得到以下3个证书
                'app_public_cert_path' => '',
                // 必填-支付宝公钥证书 路径
                'alipay_public_cert_path' => '',
                // 必填-支付宝根证书 路径
                'alipay_root_cert_path' => '',
                'return_url' => '',
                'notify_url' => '',
                // 选填-第三方应用授权token
                'app_auth_token' => '',
                // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
                'service_provider_id' => '',
                // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
                'mode' => Pay::MODE_NORMAL,
            ]
        ],
        'wechat' => [
            'default' => [
                // 必填-商户号，服务商模式下为服务商商户号
                // 可在 https://pay.weixin.qq.com/ 账户中心->商户信息 查看
                'mch_id' => '',
                // 必填-商户秘钥
                // 即 API v3 密钥(32字节，形如md5值)，可在 账户中心->API安全 中设置
                'mch_secret_key' => '',
                // 必填-商户私钥 字符串或路径
                // 即 API证书 PRIVATE KEY，可在 账户中心->API安全->申请API证书 里获得
                // 文件名形如：apiclient_key.pem
                'mch_secret_cert' => '',
                // 必填-商户公钥证书路径
                // 即 API证书 CERTIFICATE，可在 账户中心->API安全->申请API证书 里获得
                // 文件名形如：apiclient_cert.pem
                'mch_public_cert_path' => '',
                // 必填-微信回调url
                // 不能有参数，如?号，空格等，否则会无法正确回调
                'notify_url' => '',
                // 选填-公众号 的 app_id
                // 可在 mp.weixin.qq.com 设置与开发->基本配置->开发者ID(AppID) 查看
                'mp_app_id' => '',
                // 选填-小程序 的 app_id
                'mini_app_id' => '',
                // 选填-app 的 app_id
                'app_id' => '',
                // 选填-合单 app_id
                'combine_app_id' => '',
                // 选填-合单商户号
                'combine_mch_id' => '',
                // 选填-服务商模式下，子公众号 的 app_id
                'sub_mp_app_id' => '',
                // 选填-服务商模式下，子 app 的 app_id
                'sub_app_id' => '',
                // 选填-服务商模式下，子小程序 的 app_id
                'sub_mini_app_id' => '',
                // 选填-服务商模式下，子商户id
                'sub_mch_id' => '',
                // 选填-微信平台公钥证书路径, optional，强烈建议 php-fpm 模式下配置此参数
                'wechat_public_cert_path' => [
                    //'45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
                ],
                // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
                'mode' => Pay::MODE_NORMAL,
            ]
        ],
        'unipay' => [
            'default' => [
                // 必填-商户号
                'mch_id' => '',
                // 必填-商户公私钥
                'mch_cert_path' => '',
                // 必填-商户公私钥密码
                'mch_cert_password' => '',
                // 必填-银联公钥证书路径
                'unipay_public_cert_path' => '',
                // 必填，支付成功跳回页面
                'return_url' => '',
                // 必填，回调通知
                'notify_url' => '',
            ],
        ],
        'logger' => [
            'enable' => true,
            'file' => './logs/pay.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'daily', // optional, 可选 daily.
            'max_file' => 3, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
    ];

    /**
     * 获取配置
     * @param array $config
     * @param string $service
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    private static function getConfig($config = [], $service = self::WECHAT){
        //微信支付订单参数：https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_4_1.shtml
        self::$config['logger']['file'] = runtime_path('/logs/pay.log');
        self::$config['logger']['level'] = getenv('APP_DEBUG') ? 'debug' : 'info';

        $sys_config = dao_config('system.pay');
        switch ($service){
            case self::ALIPAY:
                $config = [];
                break;
            case self::UNIPAY:
                $config = [];
                break;
            default:
                $config = array_merge([
                    'app_id' => $sys_config['wx_appid'],
                    'mp_app_id' => $sys_config['wx_appid'],
                    'mch_id' => $sys_config['wx_mchid'],
                    'mch_secret_key' => $sys_config['wx_key'],
                    'mch_public_cert_path' => run_path('/data/pay/system/wx/apiclient_cert.pem'),
                    'mch_secret_cert' => run_path('/data/pay/system/wx/apiclient_key.pem')
                ], $config);
                if(! empty($sys_config['wx_p_appid'])){ //服务商模式
                    $config = array_merge($config, [
                        'app_id' => $sys_config['wx_p_appid'],
                        'mp_app_id' => $sys_config['wx_p_appid'],
                        'mch_id' => $sys_config['wx_p_mchid'],
                        'sub_app_id' => $sys_config['wx_appid'],
                        'sub_mp_app_id' => $sys_config['wx_appid'],
                        'sub_mch_id' => $sys_config['wx_mchid'],
                    ]);
                }
                $app_id = $config['app_id'];
                $base_path = run_path("/data/pay/wx/{$app_id}/");
                if(! is_dir($base_path)){
                    mkdir($base_path, 0755, true);
                }
                $cert_path = $base_path . 'apiclient_cert.pem';
                $key_path = $base_path . 'apiclient_key.pem';
                if(!file_exists($key_path) || file_get_contents($key_path) != $sys_config['wx_key_path']){
                    file_put_contents($key_path, $sys_config['wx_key_path']);
                }
                if(!file_exists($cert_path) || file_get_contents($cert_path) != $sys_config['wx_cert_path']){
                    file_put_contents($cert_path, $sys_config['wx_cert_path']);
                }
                $config = array_merge($config, [
                    'mch_public_cert_path' => $cert_path,
                    'mch_secret_cert' => $key_path
                ]);
                break;
        }
        self::$config[$service]['default'] = array_merge(self::$config[$service]['default'], $config);
        return self::$config;
    }

    /**
     * 微信退款
     * @param array $params
     * @param array $config
     * @return array
     * es: {
        "refund_id": "50000000382019052709732678859",
        "out_refund_no": "1217752501201407033233368018",
        "transaction_id": "1217752501201407033233368018",
        "out_trade_no": "1217752501201407033233368018",
     *  ...
     * }
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function wxRefund($params = [], $config = []){
        try {
            $order = [
                'out_trade_no' => $params['order_no'],
                'out_refund_no' => $params['refund_no'],
                'amount' => [
                    'refund' => $params['refund_fee'],
                    'total' => $params['total_fee'],
                    'currency' => 'CNY',
                ],
            ];
            Pay::config(self::getConfig($config, self::WECHAT));
            $result = Pay::wechat()->refund($order);
            if (empty($result['refund_id'])) {
                return self::returnError('退款失败：' . $result['message']);
            }
            return self::returnSuccess($result);
        }catch (\Exception $e){
            return  self::returnError('配置错误!');
        }
    }

    /**
     * 微信支付回调通知
     * @param array $config
     * @return array
     * @throws \Yansongda\Pay\Exception\ContainerException
     * @throws \Yansongda\Pay\Exception\InvalidParamsException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function wxCallBack($config = []){
        try {
            Pay::config(self::getConfig($config, self::WECHAT));
            $decode = Pay::wechat()->callback(\request()->post());
            if (empty($decode['resource']['ciphertext'])) {
                return self::returnError('解密失败！');
            }
            return self::returnSuccess($decode['resource']['ciphertext']);
        }catch (\Exception $e){
            return  self::returnError('配置错误!');
        }
    }

    /**
     * 扫码支付
     * @param array $params
     * @param array $config
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function wxScan($params = [], $config = []){
        try {
            Pay::config(self::getConfig($config, self::WECHAT));

            $order_no = $params['order_no'];
            $body = $params['body'];
            $amount = $params['amount'];

            $order = [
                'out_trade_no' => $order_no, //需为 string 类型
                'description' => $body,
                'amount' => [
                    'total' => $amount,
                ],
            ];
            $result = Pay::wechat()->scan($order);
            if(isset($result['code_url'])){
                return self::returnSuccess($result);
            }
            return  self::returnError($result['message'] ?? json_encode($result, JSON_UNESCAPED_UNICODE));
        }catch (\Exception $e){
            var_dump($e->getMessage());
            return  self::returnError('配置错误!');
        }
    }

    private static function returnError($msg = ''){
        return [
            'code' => 0,
            'errmsg' => $msg
        ];
    }

    private static function returnSuccess($data){
        return [
            'code' => 1,
            'data' => $data
        ];
    }

    /**
     * 公众号支付
     * @param array $params
     * @param array $config
     * @return \Yansongda\Supports\Collection
     * @throws \Yansongda\Pay\Exception\ContainerException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function wxMp($params = [], $config = []){
        self::$config['wechat']['default'] = array_merge(self::$config['wechat']['default'], $config);
        Pay::config(self::getConfig());

        $order_no = $params['order_no'];
        $body = $params['body'];
        $amount = $params['amount'];
        $openid = $params['openid'];

        $order = [
            'out_trade_no' => $order_no, //需为 string 类型
            'description' => $body,
            'amount' => [
                'total' => $amount,
            ],
            'payer' => [
                'openid' => $openid,
            ],
        ];
        return Pay::wechat()->mp($order);
    }
}