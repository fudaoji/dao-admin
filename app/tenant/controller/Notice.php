<?php
/**
 * Created by PhpStorm.
 * Script Name: Notice.php
 * Create: 2023/4/6 15:32
 * Description: 系统公告
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;


use app\common\model\Notice as NoticeM;
use app\common\service\Notice as NoticeService;
use app\common\service\Tenant as TenantService;
use app\TenantController;
use support\Request;

class Notice extends  TenantController
{
    /**
     * @var NoticeM
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this->model = new NoticeM();
    }

    /**
     * 系统公告
     * @param Request $request
     * @return mixed|\support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function index(Request $request){
        $data_list = $this->model->where('publish_time', '<=', time())
            ->order('publish_time', 'desc')
            ->paginate(15);

        $read = NoticeService::getTenantRead();
        $read_list = explode(',', trim($read['notice'], ','));
        foreach ($data_list as $k => $value){
            $value['read'] = in_array($value['id'], $read_list) ? true : false;
            $data_list[$k] = $value;
        }
        $page = $data_list->render();
        return $this->show(['data_list' => $data_list, 'page' => $page]);
    }

    /**
     * 设置已读
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setReadPost(){
        if(request()->isPost()){
            $id = input('post.id');
            NoticeService::setRead($id);
            return $this->success('');
        }
    }
}