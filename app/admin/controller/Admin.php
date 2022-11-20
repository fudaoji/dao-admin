<?php
/**
 * SCRIPT_NAME: Admin.php
 * Created by PhpStorm.
 * Time: 2020/9/6 23:23
 * Description: 管理员
 * @author: fudaoji <fdj@kuryun.cn>
 */

namespace app\admin\controller;

use app\AdminController;
use app\admin\service\Auth;

class Admin extends AdminController
{
    /**
     * @var \app\admin\model\Admin
     */
    protected $model;
    /**
     * @var \app\admin\model\AdminGroup
     */
    private $groupM;

    public function __construct(){
        parent::__construct();
        $this->model = new \app\admin\model\Admin();
        $this->groupM = new \app\admin\model\AdminGroup();
    }
    
    /**
     * 管理员列表
     * Author: Jason<dcq@kuryun.cn>
     */
    public function index(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [];
            !empty($post_data['search_key']) && $where[] = ['username|mobile|realname', 'like', '%'.$post_data['search_key'].'%'];
            if(!empty($post_data['group_id'])) {
                $where[] =['group_id', '=', $post_data['group_id']];
            }
            //非超管
            if(! Auth::isSuperAdmin()) {
                $where[] = ['id', '>', 1];
            }
            $total = $this->model->where($where)
                ->count();
            if ($total) {
                $list = $this->model->where($where)
                    ->page($post_data['page'], $post_data['limit'])
                    ->order('id', 'desc')
                    ->select();
            } else {
                $list = [];
            }

            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $group_list = Auth::getGroups();

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '账号、手机号、姓名'],
            ['type' => 'select', 'name' => 'group_id', 'title' => '角色', 'options' => [0 => '全部角色'] + $group_list]
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => '序号', 'type' => 'index'])
            ->addTableColumn(['title' => '账号', 'field' => 'username'])
            //->addTableColumn(['title' => '邮箱', 'field' => 'email'])
            ->addTableColumn(['title' => '手机号', 'field' => 'mobile'])
            ->addTableColumn(['title' => '姓名', 'field' => 'realname'])
            ->addTableColumn(['title' => '角色', 'field' => 'group_id', 'type' => 'enum', 'options' => $group_list])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'enum', 'options' => [0 => '禁用', 1 => '启用']])
            ->addTableColumn(['title' => '操作', 'width' => 220, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('edit', ['title' => '修改密码','class' => 'layui-btn layui-btn-warm layui-btn-xs','href' => url('admin/setPassword', ['id' => '__data_id__'])])
            ->addRightButton('delete');
        return $builder->show();
    }

    /**
     * 添加
     */
    public function add(){
        $groups = Auth::getGroups();
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('group_id', 'select', '角色', '角色', $groups, 'required')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('password', 'password', '密码', '6-20位', [], 'required')
            //->addFormItem('email', 'text', '邮箱', '邮箱')
            ->addFormItem('mobile', 'text', '手机', '手机')
            ->addFormItem('realname', 'text', '姓名', '姓名');

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
        $groups = Auth::getGroups();
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('group_id', 'select', '角色', '角色', $groups, 'required')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            //->addFormItem('email', 'text', '邮箱', '邮箱')
            ->addFormItem('mobile', 'text', '手机', '手机')
            ->addFormItem('realname', 'text', '姓名', '姓名')
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 编辑
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
     * @param $request
     * @param string $url
     * @param array $data
     * @return mixed
     * @Author  Doogie<461960962@qq.com>
     */
    public function savePost($request, $url='', $data=[]){
        $post_data = input('post.');
        if(!empty($post_data['password'])){
            $post_data['password'] = fa_generate_pwd($post_data['password']);
        }
        return parent::savePost($request, $url, $post_data);
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
            $post_data['id'] = $this->adminInfo('id');

            $res = $this->model->update($post_data);
            if($res){
                session([SESSION_ADMIN => null]);
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