<?php
/**
 * Created by PhpStorm.
 * Script Name: Onmessage.php
 * Create: 2023/3/16 19:23
 * Description: 回调
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;


use app\BaseController;
use app\common\service\Payment as PayService;
use app\common\service\OrderApp as OrderService;
use support\Request;
use support\Response;
use think\facade\Db;

class Onmessage extends BaseController
{
    /**
     * 应用采购回调
     * @param Request $request
     * @return Response
     * @throws \Yansongda\Pay\Exception\ContainerException
     * @throws \Yansongda\Pay\Exception\InvalidParamsException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    function orderApp(Request $request){
        $result = PayService::wxCallBack();
        var_dump($result);
        if(! empty($result['code'])){
            Db::startTrans();
            try {
                $data = $result['data'];
                $data['pay_time'] = strtotime($data['success_time']);
                OrderService::payCallBack($data);
                Db::commit();
                return new Response(200, [], 'success');
            }catch (\Exception $e){
                Db::rollback();
                var_dump($e->getMessage());
                dao_log()->error($e->getMessage());
            }
        }
        return new Response(500, [], 'fail');
    }
}