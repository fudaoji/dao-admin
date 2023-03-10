<?php
/**
 * Created by PhpStorm.
 * Script Name: Adminrule.php
 * Create: 2020/9/7 10:14
 * Description: 权限菜单
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\controller;

use app\AdminController;
use app\common\model\TenantRule as TenantRulM;

class Tenantrule extends AdminController
{
    /**
     * @var TenantRulM
     */
    protected $model;
    public function __construct()
    {
        parent::__construct();
        $this->model = new TenantRulM();
    }

    /**
     * 列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function index(){
        if(request()->isPost()){
            $list = $this->model->order('sort','desc')->select();
            return $this->success('success', '', ['total' => count($list), 'list' => $list]);
        }
        return $this->show();
    }

    /**
     * 添加
     */
    public function add(){
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增权限菜单')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('pid', 'select', '上级菜单', '上级菜单', select_list_as_tree($this->model, [['status','=', 1]], '==顶级菜单==', 'id', ['sort', 'desc']))
            ->addFormItem('type', 'select', '类型', '选择类型', $this->model->types(), 'required')
            ->addFormItem('title', 'text', '标题', '标题', [], 'required maxlength="32"')
            ->addFormItem('name', 'text', '标识', '权限控制使用', [], ' maxlength="32"')
            ->addFormItem('href', 'text', '链接', '链接', [])
            ->addFormItem('icon', 'icon', '图标', 'font-awesome图标')
            ->addFormItem('sort', 'number', '排序', '按数字从小到大排列', [], 'required');

        return $builder->show();
    }

    /**
     * 编辑
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function edit(){
        $id = input('id');
        $data = $this->model->find($id);
        if(! $data){
            $this->redirect(url('index'));
        }
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑菜单权限')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('pid', 'select', '上级菜单', '上级菜单', select_list_as_tree($this->model, [['status','=', 1]], '==顶级菜单==', 'id', ['sort', 'desc']))
            ->addFormItem('type', 'select', '类型', '选择类型', $this->model->types(), 'required')
            ->addFormItem('title', 'text', '标题', '标题', [], 'required maxlength="32"')
            ->addFormItem('name', 'text', '标识', '权限控制使用', [], ' maxlength="32"')
            ->addFormItem('href', 'text', '链接', '链接', [])
            ->addFormItem('icon', 'icon', '图标', 'font-awesome图标')
            ->addFormItem('sort', 'number', '排序', '按数字从小到大排列', [], 'required')
            ->addFormItem('status', 'radio', '状态', '状态', [1 => '显示', 0 => '隐藏'])
            ->setFormData($data);

        return $builder->show();
    }

    public function delete()
    {
        $id = input('id');
        $data = $this->model->getOne($id);
        if (!$data) {
            $this->redirect(url('index'));
        }

        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        return $builder->show();
    }
}