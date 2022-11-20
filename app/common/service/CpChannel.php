<?php
/**
 * Created by PhpStorm.
 * Script Name: CpChannel.php
 * Create: 2022/9/27 11:06
 * Description: 渠道管理
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace app\common\service;

use app\common\service\Tenant as TenantService;
use ky\EasyCps\Factory;
use app\common\model\TenantInfo as TenantInfoM;
use ky\Jtx\JdApi\JdApi;


class CpChannel extends Common
{
    /**
     * 删除渠道
     * @param array $params
     * @return array|int
     * @throws \Exception
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function delCpChannel($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return ['errmsg' => '请先填写团长账号cookie', 'code' =>  0];
        }
        $options = [
            'id' => $params['id']
        ];

        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->deleteCpChannel($options);
    }

    /**
     * 新增渠道
     * @param array $params
     * @return array|int
     * @throws \Exception
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function saveCpChannel($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return ['errmsg' => '请先填写团长账号cookie', 'code' =>  0];
        }
        $options = [
            'name' => $params['name']
        ];

        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->saveCpChannel($options);
    }

    /**
     * 设置默认渠道
     * @param array $params
     * @return array|int
     * @throws \Exception
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function setCpChannelDefault($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return ['errmsg' => '请先填写团长账号cookie', 'code' =>  0];
        }
        $options = [
            'id' => $params['id']
        ];

        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->setCpChannelDefault($options);
    }

    /**
     * 渠道列表
     * @param array $params
     * @return array|int
     * @throws \Exception
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function queryCpChannel($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return ['errmsg' => '请先填写团长账号cookie', 'code' =>  0];
        }

        $options = [
            'pageNo' => $params['page_no'] ?? 1,
            'pageSize' => $params['page_size'] ?? 20,
            'dataStartTime' => $params['begin_date']??date('Y-m-d'),
            'dataEndTime' => $params['end_date']??date('Y-m-d'),
            'id' => $params['id'] ?? '',
            'name' => $params['name'] ?? ''
        ];

        $res = JdApi::instance(['cookie' => $leader['union_cookie']])
            ->queryCpChannel($options);
        if(empty($res['totalNum'])){
            $res['result'] = [];
        }
        return  $res;
    }
}