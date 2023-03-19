<?php
/**
 * Created by PhpStorm.
 * Script Name: Wallet.php
 * Create: 2023/3/15 15:32
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;


use app\common\model\TenantWalletLog as WalletLogM;
use app\common\model\TenantWallet as WalletM;
use app\common\service\TenantWallet as WalletService;
use app\common\service\Tenant as TenantService;
use app\TenantController;
use support\Request;

class Wallet extends  TenantController
{
    /**
     * @var WalletM
     */
    protected $model;
    /**
     * @var WalletLogM
     */
    private WalletLogM $walletLogM;

    public function __construct(){
        parent::__construct();
        $this->model = new WalletM();
        $this->walletLogM = new WalletLogM();
    }

    /**
     * 变更明细
     * @param Request $request
     * @return mixed|\support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function log(Request $request){
        if($request->isPost()){
            $post_data = input('post.');
            $where = [
                ['company_id','=',  TenantService::getCompanyId()]
            ];
            $total = $this->walletLogM->where($where)->count();
            if($total){
                $list = $this->walletLogM->where($where)
                    ->page($post_data['page'], $post_data['limit'])
                    ->order('id', 'desc')
                    ->select();
            }else{
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $wallet = WalletService::getWallet(TenantService::getCompanyId());

        $builder = new ListBuilder();
        $builder->setTip("钱包总额：￥{$wallet['total']}, 可用余额：￥{$wallet['money']}, 冻结金额：￥{$wallet['frozen']}")
            ->addTableColumn(['title' => '时间', 'field' => 'create_time'])
            ->addTableColumn(['title' => '变更类型', 'field' => 'type', 'type' => 'enum', 'options' => WalletService::types()])
            ->addTableColumn(['title' => '变更金额', 'field' => 'money'])
            ->addTableColumn(['title' => '描述', 'field' => 'desc'])
            ->addTableColumn(['title' => '模块', 'field' => 'module', 'type' => 'enum', 'options' => WalletService::modules()]);
        return $builder->show();
    }
}