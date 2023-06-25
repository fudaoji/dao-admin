<?php

namespace app\tenant\controller;

use app\common\model\MediaFile as FileM;
use app\common\service\MediaGroup as GroupService;
use app\common\service\Tenant as TenantService;
use app\common\service\Media as MediaService;
use app\TenantController;

class Mediafile extends TenantController
{
    /**
     * @var FileM
     */
    protected $model;


    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new FileM();
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
        $builder->setTabNav(MediaService::mediaTabs(), MediaService::FILE)
            ->setSearch([
                ['type' => 'select', 'name' => 'group_id', 'title' => '分组', 'options' => [0=>'全部'] + GroupService::getIdToTitle()],
            ['type' => 'text', 'name' => 'search_key', 'title' => '关键词', "placeholder" => '名称']
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => '分组', 'field' => 'group_id', 'minWidth' => 100, 'type' => 'enum', 'options' => GroupService::getIdToTitle()])
            ->addTableColumn(['title' => '名称', 'field' => 'title', 'minWidth' => 80])
            ->addTableColumn(['title' => '文件链接', 'field' => 'url', 'minWidth' => 250])
            ->addTableColumn(['title' => '创建时间', 'field' => 'create_time',  'minWidth' => 200])
            ->addTableColumn(['title' => '修改时间', 'field' => 'update_time',  'minWidth' => 200])
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
        $builder->setMetaTitle('新增文件')
            ->setPostUrl(url('savePost'))
            ->addFormItem('group_id', 'chosen', '分组', '分组', GroupService::getIdToTitle())
            ->addFormItem('file', 'file_detail', '上传文件', '上传文件');

        return $builder->show();
    }

    public function edit()
    {
        $id = input('id', null);
        $data = $this->model->where([['id' ,'=', $id], ['company_id','=',TenantService::getCompanyId()]])
            ->find();

        if (!$data) {
            return $this->error('参数错误');
        }

        // 使用FormBuilder快速建立表单页面
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑文件')
            ->setPostUrl(url('savePost'))
            ->addFormItem('id', 'hidden', 'ID', 'ID')
            ->addFormItem('group_id', 'chosen', '分组', '分组', GroupService::getIdToTitle())
            ->addFormItem('file', 'file_detail', '上传文件', '上传文件', $data)
            ->setFormData($data);

        return $builder->show();
    }
}