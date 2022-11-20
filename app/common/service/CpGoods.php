<?php
/**
 * Created by PhpStorm.
 * Script Name: CpGoods.php
 * Create: 2022/9/27 11:06
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace app\common\service;

use app\common\service\Tenant as TenantService;
use ky\EasyCps\Factory;
use app\common\model\CpOrder as CpOrderM;
use app\common\model\TenantInfo as TenantInfoM;
use ky\EasyCps\JingDong\Request\JdUnionOpenGoodsQueryRequest;
use ky\EasyCps\JingDong\Request\JdUnionOpenOrderRowQueryRequest;

class CpGoods extends Common
{
    /**
     * 拉取订单
     * @param array $params
     * @return array|int
     * @throws \Exception
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function openGoodsQuery($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['app_key']) || empty($leader['app_secret']) || empty($leader['union_id'])){
            return ['code' => 0, 'errmsg' => '请先完善团长账号信息'];
        }
        $request = new JdUnionOpenGoodsQueryRequest();
        $page_no = $params['page_no'] ?? 1;
        $page_size = $params['page_size'] ?? 20;
        $request->setPageIndex($page_no);
        $request->setPageSize($page_size);
        !empty($params['sku_ids']) && $request->setSkuIds($params['sku_ids']);
        !empty($params['has_best_coupon']) && $request->setHasBestCoupon(1);
        !empty($params['fields']) && $request->setFields($params['fields']);

        $client = Factory::jingdong([
            'app_key' => $leader['app_key'],
            'app_secret' => $leader['app_secret'],
        ]);
        $res = $client->execute($request);
        if($res['code']) {
            $list = $res['data']['data'];
        }else{
            return ['code' => 0, 'errmsg' => $res['errmsg']];
        }
        return ['code' => 1, 'list' => $list];
    }
}