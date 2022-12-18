<?php
/**
 * SCRIPT_NAME: Admin.php
 * Created by PhpStorm.
 * Time: 2020/9/6 23:23
 * Description: 管理员
 * @author: fudaoji <fdj@kuryun.cn>
 */

namespace app\admin\controller;

use app\common\service\Tenant as TenantService;
use app\AdminController;
use app\common\model\TenantApp as TenantAppM;
use think\facade\Db;
use app\common\model\Tenant;
use app\common\service\App as AppService;
use Webman\Http\Request;

class Tenantapp extends AdminController
{
    /**
     * @var TenantAppM
     */
    protected $model;
    /**
     * @var Tenant
     */
    protected $tenantM;

    public function __construct(){
        parent::__construct();
        $this->model = new TenantAppM();
        $this->tenantM = new Tenant();
    }

    public function index(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [];
            !empty($post_data['search_key']) && $where[] = ['title|name|mobile|realname', 'like', '%'.$post_data['search_key'].'%'];
            $query = $this->model->alias('ta')
                ->where($where)
                ->join('app app', 'app.name=ta.app_name')
                ->join('tenant tenant', 'tenant.id=ta.company_id');
            $total = $query->count();
            if ($total) {
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->order('ta.update_time', 'desc')
                    ->field(['ta.*','app.name','app.title','tenant.realname','tenant.mobile','tenant.username'])
                    ->select();
                foreach ($list as $k => $v){
                    $v['app_info'] = $v['title'] . '('.$v['name'].')';
                    $v['tenant_info'] = $v['realname'] . '('.$v['username'].','.$v['mobile'].')';
                    $list[$k] = $v;
                }
            } else {
                $list = [];
            }

            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '应用名称或标识、客户名称或手机号']
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => '应用信息', 'field' => 'app_info', 'minWidth' => 120])
            ->addTableColumn(['title' => '客户信息', 'field' => 'tenant_info', 'minWidth' => 120])
            ->addTableColumn(['title' => '到期时间', 'field' => 'deadline', 'type' => 'datetime','minWidth' => 120])
            ->addTableColumn(['title' => '首次开通时间', 'field' => 'create_time', 'minWidth' => 140])
            ->addTableColumn(['title' => '最后修改时间', 'field' => 'update_time', 'minWidth' => 140])
            ->addTableColumn(['title' => '操作', 'minWidth' => 200, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('delete');
        return $builder->show();
    }

    /**
     * 保存数据
     * Author: fudaoji<fdj@kuryun.cn>
     * @param string $url
     * @param array $data
     * @return \support\Response
     */
    public function savePost(Request $request, $url = '', $data = []){
        if(request()->isPost()){
            $post_data = input('post.');
            $res = $this->validate($post_data, 'TenantApp.' . (isset($post_data['id']) ? 'edit' : 'add'));
            if($res !== true){
                return  $this->error($res, '', ['token' => request()->token()]);
            }

            $data = [
                'deadline' => strtotime($post_data['deadline']),
            ];
            Db::startTrans();
            try {
                if(!empty($post_data['id'])){
                    $data['id'] = $post_data['id'];
                    $this->model->update($data);
                }else{
                    $apps = explode(',', $post_data['app_name']);
                    foreach ($apps as $app_name){
                        if($this->model->where('company_id', $post_data['company_id'])
                            ->where('app_name', $app_name)->count()){
                            continue;
                        }
                        $data['company_id'] = $post_data['company_id'];
                        $data['app_name'] = $app_name;
                        $this->model->create($data);
                    }
                }
                Db::commit();
                return $this->success('保存成功');
            }catch (\Exception $e){
                Db::rollback();
                return $this->error('操作失败：' . (string)$e->getMessage(), '', ['token' => request()->token()]);
            }
        }
    }

    /**
     * 新增用户应用
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function add(){
        $company_id = input('company_id', 0);
        $company_list = TenantService::getCompanyDict();
        $app_list = AppService::getAppDict();
        $builder = new FormBuilder();
        $builder->setPostUrl(url('savePost'))
            ->addFormItem('app_name', 'chosen_multi', '应用', '应用', $app_list, 'required')
            ->addFormItem('company_id', 'chosen', '会员', '会员', $company_list, 'required')
            ->addFormItem('deadline', 'datetime', '到期时间', '到期时间', [], 'required')
            ->setFormData(['company_id' => $company_id]);
        return $builder->show();
    }

    /**
     * 编辑用户应用
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit(){
        $data = $this->model->find(input('id', 0));
        if(empty($data)){
            $this->error('数据不存在');
        }
        $data['deadline'] = date('Y-m-d H:i:s', $data['deadline']);
        $builder = new FormBuilder();
        $builder->setPostUrl(url('savePost'))
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('deadline', 'datetime', '到期时间', '到期时间', [], 'required')
            ->setFormData($data);
        return $builder->show();
    }
}