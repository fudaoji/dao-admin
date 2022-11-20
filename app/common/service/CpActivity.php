<?php
/**
 * Created by PhpStorm.
 * Script Name: CpActivity.php
 * Create: 2022/9/27 11:06
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;

use app\common\service\Tenant as TenantService;
use ky\Jtx\JdApi\JdApi;
use app\common\model\CpActivity as CpActM;
use app\common\model\CpGoods;
use app\common\model\TenantInfo as TenantInfoM;

class CpActivity extends Common
{
    /**
     * 导出商品
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function exportCpGoods($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return ['code' => 0, 'errmsg' => '请先填写团长账号cookie'];
        }
        $params = [
            'activityId' => $params['activity_id']
        ];
        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->exportCpGoods($params);
    }

    /**
     * 审核退出申请
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function examineCancelApply($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->examineCpCancelGoods($params);
    }

    /**
     * 商品退出申请列表
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function cancelGoodsList($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return ['code' => 0, 'errmsg' => '请先填写团长账号cookie'];
        }
        $options = [
            'pageNo' => $params['page_no'],
            'pageSize' => $params['page_size']
        ];
        !empty($params['activity_id']) && $options['activityId'] = $params['activity_id'];
        !empty($params['sku_id']) && $options['skuId'] = $params['sku_id'];
        !empty($params['shop_name']) && $options['shopName'] = $params['shop_name'];
        isset($params['status']) && $options['examineStatus'] = $params['status'];
        $res = JdApi::instance(['cookie' => $leader['union_cookie']])
            ->queryCpCancelGoodsList($options);
        if(empty($res['code']) || empty($res['totalNum'])){
            $res['result'] = [];
        }
        return $res;
    }

    /**
     * 审核商品
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function verifyGoods($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->verifyCpActivityGoods($params);
    }

    /**
     * 商品类目
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function getCpCategory($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->queryPromotingCategory();
    }

    /**
     * 新增活动
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function addActivity($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        unset($params['leader_id']);
        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->addCpActivity($params);
    }

    /**
     * 获取活动效果
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function getActivityEffect($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->getCpActivityEffect(['activityId' => $params['activity_id']]);
    }

    /**
     * 活动效果商品列表
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function effectGoodsList($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        $options = ['activityId' => $params['activity_id'], 'type' => 1, 'pageNo' => $params['page_no'], 'pageSize' => $params['page_size']];
        $res = JdApi::instance(['cookie' => $leader['union_cookie']])
            ->getCpActivityGoodsList($options);

        if(empty($res['code']) || empty($res['data']['totalNum'])){
            $res['data']['result'] = [];
        }
        return $res;
    }

    /**
     * 报名商品列表
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function signGoodsList($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        $options = [
            'activityId' => $params['activity_id'],
            'type' => 0,
            'pageNo' => $params['page_no'],
            'pageSize' => $params['page_size']
        ];
        !empty($params['sku_id']) && $options['skuId'] = $params['sku_id'];
        isset($params['status']) && $options['status'] = $params['status'];
        $res = JdApi::instance(['cookie' => $leader['union_cookie']])
            ->getCpActivityGoodsList($options);

        if(empty($res['code']) || empty($res['data']['totalNum'])){
            $res['data']['result'] = [];
        }
        return $res;
    }

    /**
     * 拉取商品列表
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function pullGoodsList($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        $page_no = 1;
        $page_size = 30;
        $options = ['activityId' => $params['activity_id'], 'type' => $params['type']??0, 'pageNo' => $page_no, 'pageSize' => $page_size];
        $res = JdApi::instance(['cookie' => $leader['union_cookie']])
            ->getCpActivityGoodsList($options);
        if(! $res['code']){
            return  true;
        }
        $total = $res['data']['totalNum'];
        $list = [];
        $total && $list = $res['data']['result'];

        while($total > count($list)){
            $options['pageNo']++;
            $res = JdApi::instance(['cookie' => $leader['union_cookie']])
                ->getCpActivityGoodsList($options);
            $list = array_merge($list, $res['data']['result']);
        }

        if(count($list)){
            $fields = CpGoods::FIELD_MAP;
            foreach ($list as $item){
                if(! in_array($item['examineStatus'], [1,3,4,5,6])){
                    continue;
                }
                $update = ['leader_id' => $params['leader_id']];
                //!empty($params['tenant_id']) && $update['tenant_id'] = $params['tenant_id'];
                foreach ($item as $k => $v){
                    isset($fields[$k]) && $update[$fields[$k]] = ($fields[$k] == 'image_url' ? 'https://img14.360buyimg.com/n1/' . $v : $v);
                }
                if($goods = CpGoods::where([['activity_id','=',$params['activity_id']],['sku_id','=',$item['skuId']]])
                    ->find()){
                    $update['id'] = $goods['id'];
                    $goods::update($update);
                }else{
                    CpGoods::create($update);
                }
            }
        }
        return $list;
    }

    /**
     * 获取活动详情
     * @param array $params
     * @return array|mixed|string
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public static function get($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        return JdApi::instance(['cookie' => $leader['union_cookie']])
            ->getCpActivityInfo(['activityId' => $params['activity_id']]);
    }

    /**
     * 拉取活动列表
     * @param array $params
     * @return array|mixed|string
     * @throws \think\db\exception\DbException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function pull($params = []){
        $leader = TenantInfoM::find($params['leader_id']);
        if(empty($leader['union_cookie'])){
            return '请先填写团长账号cookie';
        }
        $res = JdApi::instance(['cookie' => $leader['union_cookie']])
            ->getCpActivityList(['pageSize' => 100]);
        $acts = [];
        if($res['code']){
            $list = $res['data']['result'];
            $fields = CpActM::FIELD_MAP;
            $update = [];
            $list = array_reverse($list);
            foreach ($list as $key => $item){
                foreach ($item as $k => $v){
                    isset($fields[$k]) && $update[$fields[$k]] = $v;
                }
                if($tenant_id = (int)cache(md5($update['title'].$update['start_time'].$update['end_time']))){
                    $update['tenant_id'] = $tenant_id;
                    cache(md5($update['title'].$update['start_time'].$update['end_time']), null);
                }

                if($act = CpActM::find($item['activityId'])){
                    $update['update_time'] = time() + $key;
                    CpActM::update($update);
                }else{
                    $update['leader_id'] = $params['leader_id'];
                    $act = CpActM::create($update);
                }
                $acts[] = $act;
            }
        }
        var_dump('拉取活动: '.count($acts));
        return $acts;
    }
}