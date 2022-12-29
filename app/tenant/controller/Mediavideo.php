<?php

namespace app\tenant\controller;
use app\common\model\MediaVideo as VideoM;
use app\common\service\Media as MediaService;
use app\common\service\Tenant as TenantService;
use app\constants\Media;
use app\TenantController;

class Mediavideo extends TenantController
{
    /**
     * @var VideoM
     */
    protected $model;

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new VideoM();
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
        $builder->setTabNav(MediaService::mediaTabs(), MediaService::VIDEO)
            ->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '关键词', "placeholder" => '名称']
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => '名称', 'field' => 'title', 'minWidth' => 100])
            ->addTableColumn(['title' => '视频', 'field' => 'url', 'minWidth' => 120, "type" => 'video'])
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
        $builder->setMetaTitle('新增视频')
            ->setPostUrl(url('savePost'))
            ->addFormItem('video', 'video_detail', '上传视频', '上传视频');

        return $builder->show();
    }

    public function edit()
    {
        $id = input('id', null);
        $data = $this->model->where([['id' ,'=', $id], ['company_id','=', TenantService::getCompanyId()]])
            ->find();

        if (!$data) {
            return $this->error('参数错误');
        }

        // 使用FormBuilder快速建立表单页面
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')
            ->setPostUrl(url('savePost'))
            ->addFormItem('id', 'hidden', 'ID', 'ID')
            ->addFormItem('video', 'video_detail', '上传视频', '上传视频', $data)
            ->setFormData($data);

        return $builder->show();
    }
}