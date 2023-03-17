<?php
/**
 * Created by PhpStorm.
 * Script Name: Qrcode.php
 * Create: 2023/3/17 9:08
 * Description: 二维码服务
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;
use chillerlan\QRCode\QRCode as Generator;

class Qrcode
{
    private static $instance = null;

    static function instance(){
        if(self::$instance === null){
            self::$instance = new Generator();
        }
        return self::$instance;
    }

    /**
     * 生成普通二维码
     * @param array|string $params
     * Author: fudaoji<fdj@kuryun.cn>
     * @return mixed
     */
    static function generateCode($params = []){
        $text = empty($params['text']) ? $params : $params['text'];
        return self::instance()->render($text);
    }
}