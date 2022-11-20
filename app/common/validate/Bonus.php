<?php
// +----------------------------------------------------------------------
// | [KyPHP System] Copyright (c) 2020 http://www.kuryun.com/
// +----------------------------------------------------------------------
// | [KyPHP] 并不是自由软件,你可免费使用,未经许可不能去掉KyPHP相关版权
// +----------------------------------------------------------------------
// | Author: fudaoji <fdj@kuryun.cn>
// +----------------------------------------------------------------------

/**
 * Created by PhpStorm.
 * Script Name: Bonus.php
 * Create: 2020/5/23 下午4:22
 * Description: 订单相关验证
 * Author: fudaoji<fdj@kuryun.cn>
 */
namespace app\common\validate;
use app\common\model\Tenant as TenantM;
use ky\Helper;

class Bonus extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->rule = array_merge([
            'begin_time' => 'require|checkBeginTime',
            'end_time' => 'require|checkEndTime',
            'rid' => 'integer|min:0',
            'order_status' => 'integer|min:0',
            'search_key' => 'length:10,50'
        ],
            $this->rule
        );
        $this->message = array_merge([
            'begin_time' => 'begin_time参数错误',
            'end_time' => 'end_time参数错误',
            'search_key' => '搜索关键词非法'
        ],
            $this->message
        );
    }

    /**
     * 验证结束时间
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     * @throws \think\db\exception\DbException
     * @author: fudaoji<fdj@kuryun.cn>
     */
    protected function checkEndTime($value, $rule, $data){
        if(! Helper::checkDate($value)){
            return '结束时间格式非法';
        }
        if($value < $data['begin_time']){
            return '结束时间不能小于开始时间';
        }
        if(strtotime($value) - strtotime($data['begin_time']) > 31 * 86400){
            return '查询时间不能超过31天';
        }
        return  true;
    }

    /**
     * 验证开始时间
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     * @throws \think\db\exception\DbException
     * @author: fudaoji<fdj@kuryun.cn>
     */
    protected function checkBeginTime($value, $rule, $data){
        return  Helper::checkDate($value) ? true : '开始时间非法';
    }

    /**
     *业绩概览
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function sceneSummary(){
        return $this->only(['begin_time', 'end_time']);
    }
    /**
     *业绩每日汇总
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function sceneDailySummary(){
        return $this->only(['begin_time', 'end_time', 'rid']);
    }
    /**
     *业绩每日汇总
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function sceneOrderList(){
        return $this->only(['begin_time', 'end_time','search_key', 'order_status', 'rid','current_page','page_size']);
    }
}