<?php
/**
 * Created by PhpStorm.
 * Script Name: Queue.php
 * Create: 2023/1/11 18:42
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;

use Webman\RedisQueue\Redis;
use Webman\RedisQueue\Client;

class Queue
{
    /**
     * 任务投递
     * @param array $params
     * @return bool|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function push($params = []){
        if(empty($params['consumer'])){
            return "consumer参数错误";
        }
        $delay = empty($params['delay']) ? 0 : $params['delay'];
        $async = empty($params['async']) ? 0 : 1;
        // 队列名
        $queue = QUEUE_NAME;
        // 数据，可以直接传数组，无需序列化
        $data = $params;
        $res = true;
        // 投递消息
        if($async){
            Client::send($queue, $data, $delay);
        }else{
            Redis::send($queue, $data, $delay);
        }
        return $res;
    }


}