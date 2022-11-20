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
    
    /**
     * 列表
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

        $group_list = $this->groupM->getGroups();

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '账号、手机号、姓名'],
            ['type' => 'select', 'name' => 'group_id', 'title' => '角色', 'options' => [0 => '全部角色'] + $group_list]
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => 'ID', 'field' => 'id', 'minWidth' => 70])
            ->addTableColumn(['title' => '团长', 'field' => 'pid', 'type' => 'enum', 'options' => $this->getLeaders(), 'minWidth' => 90])
            ->addTableColumn(['title' => '账号', 'field' => 'username', 'minWidth' => 80])
            ->addTableColumn(['title' => '手机号', 'field' => 'mobile', 'minWidth' => 120])
            ->addTableColumn(['title' => '名称', 'field' => 'realname', 'minWidth' => 90])
            ->addTableColumn(['title' => '角色', 'field' => 'group_id', 'type' => 'enum', 'options' => $group_list, 'minWidth' => 90])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'enum', 'options' => [0 => '禁用', 1 => '启用'], 'minWidth' => 80])
            ->addTableColumn(['title' => '操作', 'minWidth' => 200, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('edit', ['title' => '修改密码','class' => 'layui-btn layui-btn-warm layui-btn-xs','href' => url('setPassword', ['id' => '__data_id__'])])
            ->addRightButton('delete');
        return $builder->show();
    }

    /**
     * 添加
     */
    public function add(){
        $groups = $this->groupM->getGroups();
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('group_id', 'select', '角色', '角色', $groups, 'required')
            ->addFormItem('realname', 'text', '名称', '名称')
            ->addFormItem('pid', 'chosen', '团长', '团长', [0=>'无上级']+$this->getLeaders([['status', '=',1]]))
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
        $groups = $this->groupM->getGroups();
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('realname', 'text', '名称', '名称')
            ->addFormItem('group_id', 'select', '角色', '角色', $groups, 'required')
            ->addFormItem('pid', 'chosen', '团长', '团长', [0=>'无上级']+$this->getLeaders([['status', '=',1], ['id','<>', $data['id']]]))
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('mobile', 'text', '手机', '手机')
            ->addFormItem('status', 'radio', '状态', '状态', [1 => '启用', 0 => '禁用'])
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

    private function getLeaders($map = [])
    {
        $where = [['pid', '=', 0]];
        !empty($map) && $where = array_merge($where, $map);
        return $this->model->where($where)
            ->column('realname', 'id');
    }
}