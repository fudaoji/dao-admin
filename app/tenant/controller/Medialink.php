<?php

namespace app\tenant\controller;

use app\common\model\MediaLink as LinkM;
use app\common\service\Media as MediaService;
use app\common\service\MediaGroup as GroupService;
use app\common\service\Tenant as TenantService;
use app\TenantController;

class Medialink extends TenantController
{
    /**
     * @var LinkM
     */
    protected $model;

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new LinkM();
    }

    public function index()
    {
        if (request()->isPost()) {
            $post_data = input('post.');
            $where = [
                ['company_id' ,'=', TenantService::getCompanyId()]
            ];
            !empty($post_data['search_key']) && $where[] = [
                'title', 'like', '%' . $post_data['search_key'] . '%'
            ];
            !empty($post_data['group_id']) && $where[] = ['group_id', '=', $post_data['group_id']];
            $query = $this->model->where($where);
            $total = $query->count();
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order('id', 'desc')
                    ->select();
            } else {
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setTabNav(MediaService::mediaTabs(), MediaService::LINK)
            ->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '关键词', "tip" => '标题、描述']
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => '分组', 'field' => 'group_id', 'minWidth' => 100, 'type' => 'enum', 'options' => GroupService::getIdToTitle()])
            ->addTableColumn(['title' => '标题', 'field' => 'title', 'minWidth' => 100])
            ->addTableColumn(['title' => '描述', 'field' => 'desc', 'minWidth' => 200])
            ->addTableColumn(['title' => '封面', 'field' => 'image_url', 'minWidth' => 100, "type" => 'picture'])
            ->addTableColumn(['title' => '跳转链接', 'field' => 'url', 'minWidth' => 200])
            ->addTableColumn(['title' => '创建时间', 'field' => 'create_time', 'minWidth' => 180])
            ->addTableColumn(['title' => '修改时间', 'field' => 'update_time',  'minWidth' => 180])
            ->addTableColumn(['title' => '操作', 'minWidth' => 120, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('delete');

        return $builder->show();
    }

    /**
     * 添加
     * @return mixed
     */
    public function add()
    {
        // 使用FormBuilder快速建立表单页面
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增分享链接')
            ->setPostUrl(url('savePost'))
            ->addFormItem('group_id', 'chosen', '分组', '分组', GroupService::getIdToTitle())
            ->addFormItem('title', 'text', '标题', '100字内', [], 'required maxlength=150')
            ->addFormItem('desc', 'textarea', '描述', '200字内', [], 'required maxlength=200')
            ->addFormItem('image_url', 'choose_picture', '图片', '图片比例1:1', [], 'required')
            ->addFormItem('url', 'text', '跳转链接', '跳转链接', [], 'required');

        return $builder->show();
    }

    public function edit()
    {
        $id = input('id', 0);
        $data = $this->model->where([['id' ,'=', $id], ['company_id','=', TenantService::getCompanyId()]])
            ->find();

        if (!$data) {
            return $this->error('参数错误');
        }

        // 使用FormBuilder快速建立表单页面
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑分享链接')
            ->setPostUrl(url('savePost'))
            ->addFormItem('id', 'hidden', 'ID', 'ID')
            ->addFormItem('group_id', 'chosen', '分组', '分组', GroupService::getIdToTitle())
            ->addFormItem('title', 'text', '标题', '100字内', [], 'required maxlength=100')
            ->addFormItem('desc', 'textarea', '描述', '200字内', [], 'required maxlength=150')
            ->addFormItem('image_url', 'choose_picture', '图片', '图片比例1:1', [], 'required')
            ->addFormItem('url', 'text', '跳转链接', '跳转链接', [], 'required')
            ->setFormData($data);

        return $builder->show();
    }
}