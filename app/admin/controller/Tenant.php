<?php
/**
 * SCRIPT_NAME: Admin.php
 * Created by PhpStorm.
 * Time: 2020/9/6 23:23
 * Description: 管理员
 * @author: fudaoji <fdj@kuryun.cn>
 */

namespace app\admin\controller;

use app\admin\service\Auth;
use app\AdminController;
use app\common\model\TenantGroup;
use app\common\model\Tenant as TenantM;
use Support\Request;

class Tenant extends AdminController
{
    /**
     * @var TenantM
     */
    protected $model;
    /**
     * @var TenantGroup
     */
    private $groupM;

    public function __construct(){
        parent::__construct();
        $this->model = new TenantM();
        $this->groupM = new TenantGroup();
    }
    

    public function index(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['company_id', '=', 0]
            ];
            !empty($post_data['search_key']) && $where[] = ['username|mobile|realname', 'like', '%'.$post_data['search_key'].'%'];

            //非超管
            if(! Auth::isSuperAdmin()) {
                $where[] = ['tenant.id', '>', 1];
            }
            $query = $this->model->alias('tenant')
                ->join('tenant_wallet wallet', 'wallet.id=tenant.id', 'left')
                ->where($where);
            $total = $query->count();
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->field(['tenant.*', 'wallet.money'])
                    ->order('tenant.id', 'desc')
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
            ->addTableColumn(['title' => '名称', 'field' => 'realname', 'minWidth' => 90])
            ->addTableColumn(['title' => '钱包余额', 'field' => 'money', 'minWidth' => 90])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'switch', 'minWidth' => 80])
            ->addTableColumn(['title' => '操作', 'minWidth' => 180, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('edit', ['text' => '充值', 'title' => '充值','class' => 'layui-btn-warm','href' => url('tenantwallet/recharge', ['id' => '__data_id__'])])
            ->addRightButton('edit', ['text' => '修改密码', 'title' => '修改密码','href' => url('setPassword', ['id' => '__data_id__'])])
            ->addRightButton('delete');
        return $builder->show();
    }

    /**
     * 添加
     */
    public function add(){
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('realname', 'text', '名称', '名称')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('password', 'password', '密码', '6-20位', [], 'required')
            ->addFormItem('mobile', 'text', '手机', '手机');

        return $builder->show();
    }

    /**
     * 编辑
     */
    public function edit(){
        $id = input('id');
        $data = $this->model->find($id);
        if(! $data){
            return $this->error('id参数错误');
        }

        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('realname', 'text', '名称', '名称')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('mobile', 'text', '手机', '手机')
            ->addFormItem('status', 'radio', '状态', '状态', [1 => '启用', 0 => '禁用'])
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 修改密码
     * @return mixed|\support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setPassword(){
        $id = input('id');
        $data = $this->model->find($id);
        if(! $data){
            return $this->error('id参数错误');
        }
        unset($data['password']);
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('password', 'password', '新密码', '6-20位', [], 'required')
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 保存数据
     * @param string $url
     * @param array $data
     * @return mixed
     * @Author  fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public function savePost(Request $request, $url='', $data=[]){
        $post_data = input('post.');
        if(!empty($post_data['password'])){
            $post_data['password'] = fa_generate_pwd($post_data['password']);
        }
        if(!empty($post_data['username'])){
            $exits_where = [['username', '=',$post_data['username']]];
            if(! empty($post_data[$this->pk])){
                $exits_where[] = ['id', '<>', $post_data[$this->pk]];
            }
            if($this->model->where($exits_where)->count()){
                return $this->error(dao_trans('该账号已存在'));
            }
        }

        return parent::savePost($request, $url, $post_data);
    }
}