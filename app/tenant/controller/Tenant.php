<?php
/**
 * SCRIPT_NAME: Admin.php
 * Created by PhpStorm.
 * Time: 2020/9/6 23:23
 * Description: 管理员
 * @author: fudaoji <fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\common\model\Tenant as TenantM;
use app\common\model\TenantGroup;
use app\TenantController;
use app\common\service\Tenant as TenantService;
use app\tenant\service\Auth as AuthService;
use Support\Request;;

class Tenant extends TenantController
{
    /**
     * @var TenantM
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new TenantM();
    }

    public function index(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['t.company_id', '=', TenantService::getCompanyId()]
            ];
            !empty($post_data['search_key']) && $where[] = ['username|mobile|realname', 'like', '%'.$post_data['search_key'].'%'];

            $query = $this->model->alias('t')
                ->join('tenant_group group', 'group.id=t.group_id')
                ->join('tenant_department depart', 'depart.id=t.department_id')
                ->where($where);
            $total = $query->count();
            if ($total) {
                $list = $query->field(['t.*', 'group.title as group_title', 'depart.title as depart_title'])
                    ->page($post_data['page'], $post_data['limit'])
                    ->order('t.id', 'desc')
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
            ->addTableColumn(['title' => '序号', 'type' => 'index', 'minWidth' => 70])
            ->addTableColumn(['title' => '名称', 'field' => 'realname', 'minWidth' => 100])
            ->addTableColumn(['title' => '账号', 'field' => 'username', 'minWidth' => 100])
            ->addTableColumn(['title' => '手机号', 'field' => 'mobile', 'minWidth' => 100])
            ->addTableColumn(['title' => '部门', 'field' => 'depart_title', 'minWidth' => 100])
            ->addTableColumn(['title' => '角色', 'field' => 'group_title', 'minWidth' => 100])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'enum', 'options' => [0 => '禁用', 1 => '启用']])
            ->addTableColumn(['title' => '操作', 'minWidth' => 200, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('edit', ['title' => '修改密码', 'text' => '修改密码','class' => 'layui-btn layui-btn-warm layui-btn-xs','href' => url('setpassword', ['id' => '__data_id__'])])
            ->addRightButton('delete');
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
            ->addFormItem('realname', 'text', '名称', '名称', [], 'required')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('mobile', 'text', '手机', '手机')
            ->addFormItem('department_id', 'select', '部门', '部门', AuthService::getDepartments(), 'required')
            ->addFormItem('group_id', 'select', '角色', '角色', AuthService::getGroups(), 'required')
            ->addFormItem('status', 'radio', '状态', '状态', [1 => '启用', 0 => '禁用'])
            ->setFormData($data);
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
            ->addFormItem('realname', 'text', '名称', '名称', [], 'required')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('password', 'password', '密码', '6-20位', [], 'required')
            ->addFormItem('mobile', 'text', '手机', '手机')
            ->addFormItem('department_id', 'select', '部门', '部门', AuthService::getDepartments(), 'required')
            ->addFormItem('group_id', 'select', '角色', '角色', AuthService::getGroups(), 'required');
        return $builder->show();
    }

    /**
     * 编辑
     */
    public function account(){
        $data = $this->model->find($this->tenantInfo('id'));
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('系统账号信息')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('realname', 'text', '名称', '名称', [], 'required')
            ->addFormItem('mobile', 'text', '手机', '手机', [], 'required')
            ->addFormItem('email', 'text', '邮箱', '邮箱')
            ->setFormData($data);
        return $builder->show();
    }

    /**
     * 修改密码
     */
    public function setPassword(){
        if(request()->isPost()){
            $post_data = input('post.');
            if(empty($post_data['password'])){
                return $this->error('请填写新密码');
            }
            $post_data['password'] = fa_generate_pwd($post_data['password']);
            $res = $this->model->update($post_data);
            if($res){
                return $this->success('操作成功！');
            }else{
                return $this->error('操作失败，请联系管理员');
            }
        }
        $id = input('id');
        $data = $this->model->find($id);
        if(! $data){
            return $this->error('id参数错误');
        }
        unset($data['password']);
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')  //设置页面标题
            ->setPostUrl(url('setPassword')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('password', 'password', '新密码', '6-20位', [], 'required')
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 保存数据
     * @param Request $request
     * @param string $url
     * @param array $data
     * @return mixed
     * @Author  Doogie<461960962@qq.com>
     */
    public function savePost(Request $request, $url='', $data=[]){
        $post_data = input('post.');
        if(!empty($post_data['password'])){
            $post_data['password'] = fa_generate_pwd($post_data['password']);
        }

        $res = $this->validate($post_data, "Tenant.edit");
        if($res !== true){
            return $this->error($res, '', ['token' => token()]);
        }

        try {
            if(empty($post_data[$this->pk])){
                $post_data['company_id'] = TenantService::getCompanyId();
                $res = $this->model->create($post_data);
            }else {
                $res = $this->model->update($post_data);
            }
            if($res){
                return $this->success("操作成功!", '');
            }else{
                return $this->error("未修改数据无需提交", null, ['token' => token()]);
            }
        }catch (\Exception $e){
            $msg = $e->getMessage();
            return $this->error($msg, null, ['token' => token()]);
        }
    }

    /**
     * 修改个人密码
     * @return mixed
     * Author: Doogie<fdj@kuryun.cn>
     * @throws \think\Exception
     */
    public function setPersonPw(){
        if(request()->isPost()){
            $post_data = ['password' => input('post.password')];
            if(!empty($post_data['password'])){
                $post_data['password'] = fa_generate_pwd($post_data['password']);
            }
            $post_data['id'] = $this->tenantInfo('id');
            $res = $this->model->update($post_data);
            if($res){
                session([SESSION_TENANT => null]);
                return $this->success('密码修改成功，请重新登录', url('auth/login'));
            }else{
                return $this->error('系统出错');
            }
        }
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('修改个人密码')  //设置页面标题
        ->setPostUrl(url('setPersonPw')) //设置表单提交地址
        ->addFormItem('password', 'password', '新密码', '6-20位', [], 'required minlength=6 maxlength=20');

        return $builder->show();
    }
}