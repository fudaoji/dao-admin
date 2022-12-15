<?php
/**
 * Created by PhpStorm.
 * Script Name: Admingroup.php
 * Create: 2:52 下午
 * Description:
 * Author: Jason<dcq@kuryun.cn>
 */

namespace app\admin\controller;

use app\AdminController;
use app\common\model\AppCate as AppCateM;

class Appcate extends AdminController
{
    /**
     * 数据库实例
     * @var AppCateM
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new AppCateM();
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
            $where = [];
            !empty($post_data['search_key']) && $where[] = ['title', 'like', '%'.$post_data['search_key'].'%'];
            $query = $this->model->where($where);
            $total = $query->count();
            if($total){
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order('sort', 'desc')
                    ->select();
            }else{
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '名称']
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => 'ID', 'field' => 'id'])
            ->addTableColumn(['title' => '名称', 'field' => 'title'])
            ->addTableColumn(['title' => '排序', 'field' => 'sort'])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'switch'])
            ->addTableColumn(['title' => '操作', 'width' => 100, 'type' => 'toolbar'])
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
            ->addFormItem('title', 'text', '名称', '名称', [], 'required minlength=1 maxlength=20')
            ->addFormItem('sort', 'number', '排序', '排序', [], 'required min=0');

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
            ->addFormItem('title', 'text', '名称', '名称', [], 'required minlength=1 maxlength=20')
            ->addFormItem('sort', 'number', '排序', '排序', [], 'required min=0')
            ->addFormItem('status', 'radio', '状态', '状态', [1 => '启用', 0 => '禁用'])
            ->setFormData($data);

        return $builder->show();
    }
}