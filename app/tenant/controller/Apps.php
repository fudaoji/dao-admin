<?php
/**
 * Created by PhpStorm.
 * Script Name: Apps.php
 * Create: 2022/12/15 8:14
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\common\constant\Platform;
use app\common\model\App;
use app\common\model\AppCate;
use app\common\model\TenantApp;
use app\common\service\Tenant as TenantService;
use app\common\service\App as AppService;
use app\TenantController;
use think\facade\Db;

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

    /**
     * 应用采购下单
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function addOrderPost(){
        if(request()->isPost()){
            $post_data = input('post.');
            $app = AppService::getAppInfo($post_data['id']);
            if(!$app){
                $this->error('参数错误');
            }

            $return = ['app' => $app];
            if($post_data['type'] == 'new'){
                $msg = '应用开通成功';
            }else{
                $msg = '续费成功';
            }

            $url = '';
            Db::startTrans();
            try {
                if($app['price'] <= 0){
                    AppService::afterBuyApp([
                        'app' => $app,
                        'company_id' => TenantService::getCompanyId()
                    ]);
                }else{
                    //todo 下单，前台走支付
                }
                Db::commit();
                return $this->success($msg, $url, $return);
            }catch (\Exception $e){
                dao_log()->error($e->getMessage());
                Db::rollback();
                return $this->error('系统错误，请刷新重试');
            }
        }
    }

    /**
     * 应用详情
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function appDetail(){
        $id = input('id', 0);
        $data = AppService::getAppInfo($id);
        if(! $data){
            return $this->error('数据不存在');
        }

        $data['snapshot'] = explode('|', $data['snapshot']);
        $assign = [
            'tenant_app' => $this->tenantAppM->where('company_id', TenantService::getCompanyId())
                ->where('app_name' , $data['name'])
                ->find(),
            'data' => $data
        ] ;
        return $this->show($assign);
    }

    /**
     * 应用采购
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DbException
     */
    public function store(){
        $type = input('type', '');
        $search_key = input('search_key', '');
        $cate = input('cate', 'all');
        $where = [
            ['a.status', '=', 1]
        ];
        $type && $where[] = ['a.type', 'like', '%'.$type.'%'];
        $search_key && $where[] = ['a.title|a.desc', 'like', '%'.$search_key.'%'];
        $cate !== 'all' && $where[] = ['a.cates', 'like', '%'.$cate.'%'];
        $page_size = 12;
        $data_list = $this->model->alias('a')
            ->join('app_info ai', 'ai.id=a.id')
            ->field(['a.id','a.logo','a.title','a.desc','a.type','ai.sale_num_show','ai.price'])
            ->order(['ai.sale_num_show' => 'desc'])
            ->where($where)
            ->paginate($page_size);
        $page = $data_list->appends(['type' => $type, 'search_key' => $search_key])->render();
        $cates = AppService::getAppCateDict();
        $assign = [
            'data_list' => $data_list,
            'type' => $type,
            'search_key' => $search_key,
            'page' => $page,
            'cates' => ['all' => '全部'] + array_combine($cates, $cates),
            'cate' => $cate,
            'types' => Platform::types()
        ];
        return $this->show($assign);
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

        $page_size = 12;
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
                'ta.*','app.logo','app.desc','app.name','app.title','app.tenant_url','app.tenant_url_type',
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
    public function overtime(){
        $company_id = TenantService::getCompanyId();
        $page_size = 12;
        $status = input('status', -1);
        $search_key = input('search_key', '');
        $where = [
            ['ta.deadline', '<=', time()],
            ['ta.company_id', '=', $company_id],
        ];

        $search_key && $where[] = ['app.title|app.desc', 'like', '%'.$search_key.'%'];
        $query = $this->tenantAppM->alias('ta')
            ->where($where)
            ->join('app app', 'app.name=ta.app_name')
            ->join('tenant tenant', 'tenant.id=ta.company_id');
        $data_list = $query->order('ta.update_time', 'desc')
            ->field([
                'ta.*','app.logo','app.desc','app.name','app.title','app.tenant_url','app.tenant_url_type',
                'tenant.realname', 'tenant.mobile','tenant.username'
            ])
            ->paginate($page_size);
        $page = $data_list->appends(['status' => $status, 'search_key' => $search_key])->render();

        $assign = [
            'data_list' => $data_list,
            'search_key' => $search_key,
            'page' => $page
        ];
        return $this->show($assign);
    }
}