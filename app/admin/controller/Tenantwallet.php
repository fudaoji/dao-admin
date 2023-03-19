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
use app\common\model\Tenant as TenantM;
use app\common\model\TenantWallet as WalletM;
use app\common\service\Tenant as TenantService;
use app\common\service\TenantWallet as WalletService;
use Support\Request;

class Tenantwallet extends AdminController
{
    /**
     * @var WalletM
     */
    protected $model;
    /**
     * @var TenantM
     */
    private TenantM $tenantM;

    public function __construct(){
        parent::__construct();
        $this->model = new WalletM();
        $this->tenantM = new TenantM();
    }

    /**
     * 充值
     * @return mixed|\support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function recharge()
    {
        if(request()->isPost()){
            $post_data = input('post.');
            $params = [
                'company_id' => $post_data['id'],
                'module' => WalletService::RECHARGE_SYS
            ];
            if($post_data['type'] == 1){
                $params['money'] = $post_data['num'];
                $params['desc'] = '管理员代充￥' . $post_data['num'];
            }else{
                $params['money'] = -$post_data['num'];
                $params['desc'] = '管理员代扣￥' . $post_data['num'];
            }
            if(WalletService::saveChange($params)){
                return $this->success('操作成功！');
            }
            return $this->error('操作失败！', '', ['token' => token()]);
        }

        $id = input('id', null);
        $data = WalletService::getWallet($id);

        if(!$data) {
            $this->error('参数错误');
        }

        $data['type'] = 1;
        // 使用FormBuilder快速建立表单页面
        $builder = new FormBuilder();
        $builder->setPostUrl(url('recharge'))
            ->setMetaTitle('充值')      //设置页面标题
            ->addFormItem('id', 'hidden', 'ID', 'ID', [], 'required')
            ->addFormItem('type', 'radio', '类型', '类型', [1 => '增加', 2 => '扣除'], 'required')
            ->addFormItem('num', 'number', '变动金额', '变动金额', [], 'required min=0.01')
            ->setFormData($data);

        return $builder->show();
    }

    public function index(Request $request){
        if($request->isPost()){
            $post_data = input('post.');
            $where = [
                ['company_id', '=', 0]
            ];
            !empty($post_data['search_key']) && $where[] = ['username|mobile|realname', 'like', '%'.$post_data['search_key'].'%'];
            $query = $this->tenantM->alias('tenant')
                ->where($where)
                ->join('tenant_wallet wallet', 'tenant.id=wallet.id', 'left');
            $total = $query->count();
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order('wallet.id', 'desc')
                    ->field(['wallet.*','tenant.realname','tenant.mobile','tenant.username','tenant.id'])
                    ->select();
            } else {
                $list = [];
            }

            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '账号、手机号、名称']
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => 'ID', 'field' => 'id', 'minWidth' => 70])
            ->addTableColumn(['title' => '账号', 'field' => 'username', 'minWidth' => 80])
            ->addTableColumn(['title' => '手机号', 'field' => 'mobile', 'minWidth' => 120])
            ->addTableColumn(['title' => '姓名', 'field' => 'realname', 'minWidth' => 90])
            ->addTableColumn(['title' => '钱包总额', 'field' => 'total'])
            ->addTableColumn(['title' => '钱包冻结额', 'field' => 'frozen'])
            ->addTableColumn(['title' => '钱包余额', 'field' => 'money'])
            ->addTableColumn(['title' => '操作', 'minWidth' => 120, 'type' => 'toolbar'])
            ->addRightButton('edit', ['text' => '变更明细', 'href' => url('tenantwalletlog/index', ['company_id' => '__data_id__'])])
            ->addRightButton('edit', ['text' => '充值', 'class' => 'layui-btn-warm','href' => url('recharge', ['id' => '__data_id__'])]);
        return $builder->show();
    }
}