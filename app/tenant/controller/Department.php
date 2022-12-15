<?php
/**
 * Created by PhpStorm.
 * Script Name: Admingroup.php
 * Create: 2:52 下午
 * Description:
 * Author: Jason<dcq@kuryun.cn>
 */

namespace app\tenant\controller;

use app\TenantController;
use app\common\model\TenantDepartment;
use app\common\service\Tenant as TenantService;

class Department extends TenantController
{
    /**
     * @var TenantDepartment
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new TenantDepartment();
    }

    /**
     * 列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['company_id', '=', TenantService::getCompanyId()]
            ];
            !empty($post_data['search_key']) && $where[] = ['title', 'like', '%'.$post_data['search_key'].'%'];
            $total = $this->model->where($where)
                ->count();
            if($total){
                $list = $this->model->where($where)
                    ->page($post_data['page'], $post_data['limit'])
                    ->order('sort', 'desc')
                    ->select();
            }else{
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '部门名称']
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => 'ID', 'field' => 'id', 'minWidth' => 60])
            ->addTableColumn(['title' => '部门名称', 'field' => 'title', 'minWidth' => 120])
           ->addTableColumn(['title' => '备注信息', 'field' => 'remark', 'minWidth' => 100])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'enum', 'options' => [1 => '启用', 0 => '禁用'], 'minWidth' => 70])
            ->addTableColumn(['title' => '排序', 'field' => 'sort'])
            ->addTableColumn(['title' => '操作', 'minWidth' => 150, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('delete');
        return $builder->show();
    }

    /**
     * 添加
     * Author: Doogie <461960962@qq.com>
     */
    public function add(){
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('title', 'text', '部门名称', '部门名称', [], 'required')
            ->addFormItem('remark', 'textarea', '备注', '备注');

        return $builder->show();
    }

    /**
     * 编辑
     * Author: Doogie <461960962@qq.com>
     */
    public function edit(){
        $id = input('id');
        $data = $this->model->find($id);
        if(! $data){
            $this->error('id参数错误');
        }
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('title', 'text', '部门名称', '部门名称', [], 'required')
            ->addFormItem('remark', 'textarea', '备注', '备注')
            ->addFormItem('sort', 'number', '排序', '排序', [], 'required min=0')
            ->setFormData($data);

        return $builder->show();
    }
}