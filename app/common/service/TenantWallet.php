<?php
/**
 * Created by PhpStorm.
 * Script Name: TenantWallet.php
 * Create: 2023/3/15 9:11
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;

use app\common\model\TenantWallet as WalletM;
use app\common\model\TenantWalletLog as LogM;
use think\facade\Db;

class TenantWallet extends Common
{
    //type字典
    const INCOME = 1;
    const EXPEND = 0;
    //module字典
    const ORDER_APP = 'order_app';
    const RECHARGE = 'recharge';
    const RECHARGE_SYS = 'recharge_sys';

    /**
     * 类型
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function types($id = null){
        $list = [
            self::INCOME => '收入',
            self::EXPEND => '支出',
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }

    /**
     * 用途
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function modules($id = null){
        $list = [
            self::ORDER_APP => '应用采购',
            self::RECHARGE_SYS => '系统操作'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }

    /**
     * 退款钱包
     * @param array $params
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function refundWallet($params = []){
        Db::startTrans();
        try {
            $company_id = $params['company_id'];
            $money = $params['money'];
            $desc = $params['desc'] ?? '';
            $module = $params['module'] ?? self::ORDER_APP;

            $user_wallet = self::getWallet($company_id);
            $update = [
                'id' => $company_id,
                'money' => $user_wallet['money'] + $money
            ];
            WalletM::update($update);

            //record log
            LogM::insert([
                'company_id' => $company_id,
                'type' => self::INCOME,
                'money' => $money,
                'desc'  => $desc,
                'module' => $module,
                'create_time' => time()
            ]);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            dao_log()->error($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 成交冻结金额
     * @param array $params
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function releaseFrozen($params = []){
        Db::startTrans();
        try {
            $company_id = $params['company_id'];
            $money = $params['money'];
            $desc = $params['desc'] ?? '';
            $module = $params['module'] ?? self::ORDER_APP;

            $user_wallet = self::getWallet($company_id);
            $update = [
                'id' => $company_id
            ];
            $update['frozen'] = max($user_wallet['frozen'] - $money, 0);
            WalletM::update($update);

            //record log
            LogM::insert([
                'company_id' => $company_id,
                'type' => self::EXPEND,
                'money' => $money,
                'desc'  => $desc,
                'module' => $module,
                'create_time' => time()
            ]);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            dao_log()->error($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 撤销冻结金额
     * @param int $company_id
     * @param int $money
     * @return \think\Model
     * @throws \think\db\exception\DbException Author: fudaoji<fdj@kuryun.cn>
     */
    static function cancelFrozen($company_id = 0, $money = 0){
        return WalletM::update([
            'id' => $company_id,
            'frozen' => Db::raw('frozen-' . $money),
            'money' => Db::raw('money+' . $money)
        ]);
    }

    /**
     * 冻结金额
     * @param int $company_id
     * @param int $money
     * @return \think\Model
     * @throws \think\db\exception\DbException Author: fudaoji<fdj@kuryun.cn>
     */
    static function frozenMoney($company_id = 0, $money = 0){
        return WalletM::update([
            'id' => $company_id,
            'frozen' => Db::raw('frozen+' . $money),
            'money' => Db::raw('money-' . $money)
        ]);
    }

    /**
     * 获取用户钱包
     * @param int $id
     * @param null $column
     * @return int|string|\think\Model
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getWallet($id = 0, $column = null){
        $data = WalletM::find($id);
        if(empty($data)){
            $data = ['id' => $id, 'create_time' => time(), 'total' => 0, 'frozen' => 0, 'money' => 0];
            WalletM::insert($data);
        }
        $data = $data->toArray();
        return isset($data[$column]) ? $data[$column] : $data;
    }

    /**
     * 钱包变化
     * @param array $params
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function saveChange($params = []){
        Db::startTrans();
        try {
            $company_id = $params['company_id'];
            $change = $params['money'];
            $desc = $params['desc'] ?? '';
            $module = $params['module'] ?? self::ORDER_APP;

            $user_wallet = self::getWallet($company_id);
            $update = [
                'id' => $company_id
            ];

            $money = abs($params['money']);
            if($change > 0){
                $update['total'] = Db::raw('total+' . $money);
                $update['money'] = Db::raw('money+' . $money);
            }else{
                $update['money'] = max($user_wallet['money'] - $money, 0);
            }
            WalletM::update($update);

            //record log
            LogM::insert([
                'company_id' => $company_id,
                'type' => $change > 0 ? self::INCOME : self::EXPEND,
                'money' => $money,
                'desc'  => $desc,
                'module' => $module,
                'create_time' => time()
            ]);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            dao_log()->error($e->getMessage());
            return false;
        }
        return true;
    }
}