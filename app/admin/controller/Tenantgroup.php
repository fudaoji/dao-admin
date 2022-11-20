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
use app\common\model\TenantRule;
use app\common\model\TenantGroup as TenantGroupM;

class Tenantgroup extends AdminController
{
    /**
     * @var TenantRule
     */
    private $ruleM;
    /**
     * @var TenantGroupM
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new TenantGroupM();
        $this->ruleM = new TenantRule();
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
                    ->order('sort', 'desc')
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
            ->addTopButton('addnew')
            ->addTableColumn(['title' => 'ID', 'field' => 'id', 'minWidth' => 60])
            ->addTableColumn(['title' => '角色名称', 'field' => 'title', 'minWidth' => 120])
            ->addTableColumn(['title' => '角色标识', 'field' => 'name', 'minWidth' => 80])
            ->addTableColumn(['title' => '上级', 'field' => 'pid', 'type' => 'enum', 'options' => [0 => '无上级']+$this->getGroups(),'minWidth' => 80])
            ->addTableColumn(['title' => '备注信息', 'field' => 'remark', 'minWidth' => 100])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'enum', 'options' => [1 => '启用', 0 => '禁用'], 'minWidth' => 70])
            ->addTableColumn(['title' => '排序', 'field' => 'sort'])
            ->addTableColumn(['title' => '操作', 'minWidth' => 150, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('edit', ['title' => '授权','class' => 'layui-btn layui-btn-xs', 'href' => url('auth', ['group_id' => '__data_id__'])])
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
            ->addFormItem('name', 'text', '角色标识', '角色标识', [], '')
            ->addFormItem('pid', 'select', '上级', '上级', $this->getGroups(['status' => 1]), '')
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
            ->addFormItem('name', 'text', '角色标识', '角色标识', [], '')
            ->addFormItem('pid', 'select', '上级', '上级', $this->getGroups(['status' => 1]), '')
            ->addFormItem('remark', 'textarea', '备注', '备注')
            ->addFormItem('sort', 'number', '排序', '排序', [], 'required min=0')
            ->setFormData($data);

        return $builder->show();
    }

    private function getGroups($where = []){
        return $this->model->order(['sort' => 'desc'])
            ->column('title','id');
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