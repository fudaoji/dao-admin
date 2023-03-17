<?php

namespace app;

use app\common\service\Tenant as TenantService;
use app\tenant\service\Auth;
use Support\Request;

class TenantController extends BaseController
{
    /**
     * 数据库实例
     * @var BaseModel
     */
    protected $model = null;

    /**
     * 控制器方法
     * @var string
     */
    public $action = null;

    /**
     * 控制器/方法名
     * @var string
     */
    public $method = null;

    /**
     * 获取模板
     * @access   protected
     * @var      string
     */
    public $theme = 'default';

    protected $pk = 'id';
    protected $captchaKey = 'captchaTenant';
    protected $insertCompanyId = true;

    /**
     * 构造函数
     */
    public function __construct()
    {
        //只有在启动时执行
        parent::__construct();
    }

    /**
     * 商户id条件
     * @param string $alias
     * @param array $tenant_info
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function tenantWhere($alias = '', $tenant_info = []){
        $alias = $alias ? $alias.'.' : '';
        $tenant_info = empty($tenant_info) ? $this->tenantInfo() : $tenant_info;
        return [$alias . 'company_id', '=', TenantService::getCompanyId($tenant_info)];
    }

    /**
     * 租户信息
     * @param null $key
     * @return mixed|null
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function tenantInfo($key = null){
        $admin = request()->session()->get(SESSION_TENANT);
        return $key!==null ? $admin[$key] : $admin;
    }

    /**
     * 设置一条或者多条数据的状态
     * @Author  fudaoji<fdj@kuryun.cn>
     */
    public function setStatus() {
        $ids = input('ids');
        $status = input('status');

        if (empty($ids)) {
            return $this->error('请选择要操作的数据');
        }

        $ids = (array) $ids;
        if($status == 'delete'){
            $where = [[$this->pk, 'in', $ids]];
            $this->insertCompanyId && $where[] = $this->tenantWhere();
            if($this->model->where($where)->delete()){
                return $this->success('删除成功');
            }else{
                return $this->error('删除失败');
            }
        }else{
            $arr = [];
            $msg = [
                'success' => '操作成功！',
                'error'   => '操作失败！',
            ];
            $data['status'] = abs(input('val', 0) - 1);
            $this->insertCompanyId && $data['company_id'] = TenantService::getCompanyId();
            foreach($ids as $id){
                $data[$this->pk] = $id;
                $arr[] = $data;
            }
            if($this->model->saveAll($arr)){
                return $this->success($msg['success']);
            }else{
                return $this->error($msg['error']);
            }
        }
    }

    /**
     * 保存数据
     * @param Request $request
     * @param string $jump
     * @param array $data
     * @return mixed
     */
    public function savePost(Request $request, $jump = '', $data = []){
        $post_data = $data ? $data : $request->post();
        try {
            if(empty($post_data[$this->pk])){
                $this->insertCompanyId && $post_data['company_id'] = TenantService::getCompanyId();
                $res = $this->model->create($post_data);
            }else {
                $res = $this->model->update($post_data);
            }
            if($res){
                return $this->success("操作成功!", $jump);
            }else{
                return $this->error("未修改数据无需提交", null, ['token' => token()]);
            }
        }catch (\Exception $e){
            $msg = $e->getMessage();
            return $this->error($msg, null, ['token' => token()]);
        }
    }
}