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
use ky\Tree;

class Admingroup extends AdminController
{
    /**
     * @var \app\admin\model\AdminRule
     */
    private $ruleM;
    /**
     * @var \app\admin\model\AdminGroup
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new \app\admin\model\AdminGroup();
        $this->ruleM = new \app\admin\model\AdminRule();
    }

    /**
     * 分组列表
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
            $total = $this->model->where($where)
                ->count();
            if($total){
                $list = $this->model->where($where)
                    ->page($post_data['page'], $post_data['limit'])
                    ->order('sort', 'asc')
                    ->select();
            }else{
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '角色名称']
        ])
            //->setAuth(['super' => Auth::isSuperAdmin(), 'auth_list' => Auth::getAuthList()])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => 'ID', 'field' => 'id'])
            ->addTableColumn(['title' => '角色名称', 'field' => 'title'])
            ->addTableColumn(['title' => '备注信息', 'field' => 'remark'])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'enum', 'options' => [1 => '启用', 0 => '禁用']])
            ->addTableColumn(['title' => '操作', 'width' => 150, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('edit', ['text' => '授权','class' => 'layui-btn layui-btn-xs', 'href' => url('auth', ['group_id' => '__data_id__'])])
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
            ->addFormItem('title', 'text', '角色名称', '角色名称', [], 'required')
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
            ->addFormItem('title', 'text', '角色名称', '角色名称', [], 'required')
            ->addFormItem('remark', 'textarea', '备注', '备注')
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 授权
     * Author: Jason<dcq@kuryun.cn>
     */
    public function auth() {
        if(request()->isPost()) {
            $post_data = input('post.');
            $update_data = [
                'id' => $post_data['id'],
                'rules' => $post_data['rules']
            ];
            $result = $this->model->update($update_data);
            if($result) {
                return $this->success('授权成功', url('index'), ['result' => $result]);
            }else {
                return $this->error('授权失败');
            }
        }
        $group_id = input('group_id', 0);
        $data = $this->model->find($group_id);
        if(! $data) {
            return $this->error('数据不存在');
        }
        $data['rules'] = explode(',', $data['rules']);

        return $this->show(['data' => $data]);
    }

    /**
     * 节点树
     * Author: Jason<dcq@kuryun.cn>
     */
    public function getRulesTree() {
        if(request()->isPost()){
            $post_data = input('post.');
            $rules = $this->ruleM->where([['status','=', 1]])
                ->order('sort', 'desc')
                ->field('id, pid, title, href')
                ->select();
            $group = $this->model->find($post_data['group_id']);
            $group_rules = explode(',', $group['rules']);

            //插入layui展开参数
            foreach ($rules as $k => &$item) {
                $item['spread'] = true;
                if($item['href']) {
                    $item['title'] = $item['title'] . '【' . $item['href'] . '】';
                }
                //设置数据源中勾选的叶子节点checked属性为true
                $total = $this->ruleM->where([['pid','=',$item['id'], 'status' ,'=',1]])
                    ->count();
                if(in_array($item['id'], $group_rules) && !$total) {
                    $item['checked'] = true;
                }else {
                    $item['checked'] = false;
                }
                $rules[$k] = $item;
            }
            $Tree = new Tree();
            $rules_tree = $Tree->listToTree($rules);
            return $this->success('success', '', ['rules_tree' => $rules_tree]);
        }
    }
}