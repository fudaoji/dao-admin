<?php
/**
 * SCRIPT_NAME: Tenantwallet.php
 * Created by PhpStorm.
 * Time: 2020/9/6 23:23
 * Description: 客户钱包
 * @author: fudaoji <fdj@kuryun.cn>
 */

namespace app\admin\controller;

use app\AdminController;
use app\common\model\TenantWalletLog as LogM;
use app\common\model\TenantWallet as WalletM;
use app\common\service\Tenant as TenantService;
use app\common\service\TenantWallet as WalletService;
use Support\Request;

class Tenantwalletlog extends AdminController
{
    /**
     * @var LogM
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new LogM();
    }

    public function index(Request $request){
        $user_id = input('company_id', 0);
        if($request->isPost()){
            $post_data = input('post.');
            $where = [
                ['company_id','=',  $user_id]
            ];
            $total = $this->model->where($where)->count();
            if($total){
                $list = $this->model->where($where)
                    ->page($post_data['page'], $post_data['limit'])
                    ->order('id', 'desc')
                    ->select();
            }else{
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setDataUrl(url('index', ['company_id' => $user_id]))
            ->addTableColumn(['title' => '时间', 'field' => 'create_time'])
            ->addTableColumn(['title' => '变更类型', 'field' => 'type', 'type' => 'enum', 'options' => WalletService::types()])
            ->addTableColumn(['title' => '变更金额', 'field' => 'money'])
            ->addTableColumn(['title' => '描述', 'field' => 'desc'])
            ->addTableColumn(['title' => '模块', 'field' => 'module', 'type' => 'enum', 'options' => WalletService::modules()]);
        return $builder->show();
    }
}