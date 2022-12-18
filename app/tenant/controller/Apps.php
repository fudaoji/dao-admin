<?php
/**
 * Created by PhpStorm.
 * Script Name: Apps.php
 * Create: 2022/12/15 8:14
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\common\model\App;
use app\common\model\TenantApp;
use app\common\service\Tenant as TenantService;
use app\TenantController;

class Apps extends TenantController
{
    /**
     * @var App
     */
    protected $model;
    /**
     * @var TenantApp
     */
    private TenantApp $tenantAppM;

    public function __construct(){
        parent::__construct();
        $this->model = new App();
        $this->tenantAppM = new TenantApp();
    }

    public function index(){
        $company_id = TenantService::getCompanyId();
        if(request()->isPost()){ //开启关闭
            $id = input('post.id');
            if(empty($ta = $this->tenantAppM->where('id' , $id)
                ->where('company_id', $company_id)
                ->find())){
                $this->error('数据不存在');
            }
            $this->tenantAppM->update(['id' => $id, 'status' => abs($ta['status'] - 1)]);
            return $this->success('操作成功');
        }

        $page_size = 20;
        $status = input('status', -1);
        $search_key = input('search_key', '');
        $where = [
            ['ta.deadline', '>', time()],
            ['ta.company_id', '=', $company_id],
        ];

        $status != -1 && $where[] = ['ta.status', '=', $status];
        $search_key && $where[] = ['app.title|app.desc', 'like', '%'.$search_key.'%'];
        $query = $this->tenantAppM->alias('ta')
            ->where($where)
            ->join('app app', 'app.name=ta.app_name')
            ->join('tenant tenant', 'tenant.id=ta.company_id');
        $data_list = $query->order('ta.update_time', 'desc')
            ->field([
                'ta.*','app.logo','app.desc','app.name','app.title','app.admin_url','app.admin_url_type',
                'tenant.realname', 'tenant.mobile','tenant.username'
            ])
            ->paginate($page_size);
        $page = $data_list->appends(['status' => $status, 'search_key' => $search_key])->render();

        $assign = [
            'data_list' => $data_list,
            'search_key' => $search_key,
            'page' => $page,
            'status' => $status
        ];
        return $this->show($assign);
    }
}