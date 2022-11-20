<?php

namespace app;

use app\common\model\TenantInfo;
use app\common\service\Tenant as TenantService;
use app\tenant\service\Auth;
use Webman\Http\Request;

class TenantController extends BaseController
{
    /**
     * 数据库实例
     * @var BaseModel
     */
    protected $model = null;

    /**
     * 控制器/类名
     * @var string
     */
    public $controller = null;

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

    /**
     * 构造函数
     */
    public function __construct()
    {
        //只有在启动时执行
        parent::__construct();
    }

    /**
     * 二招条件
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
        if(TenantService::getLevel($tenant_info) == 3){
            $where = [$alias . 'tenant_id', '=', $tenant_info['id']];
        }else{
            $where = [$alias . 'tenant_id', 'in', TenantService::getTeamIds('id','', $tenant_info)];
        }
        return $where;
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
     * 租户扩展信息
     * @param null $key
     * @return mixed|null
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function tenantExtendInfo($key = null){
        $admin = request()->session()->get(SESSION_TENANT);
        $extend = TenantInfo::find($admin['id']);
        return $extend ? ($key!==null ? $extend[$key] : $extend) : '';
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
            if($this->model->where([[$this->pk, 'in', $ids]])->delete()){
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
            switch ($status) {
                case 'forbid' :  // 禁用条目
                    $data['status'] = 0;
                    break;
                case 'resume' :  // 启用条目
                    $data['status'] = 1;
                    break;
                case 'hide' :  // 隐藏条目
                    $data['status'] = 2;
                    break;
                case 'show' :  // 显示条目
                    $data['status'] = 1;
                    break;
                case 'recycle' :  // 移动至回收站
                    $data['status'] = 1;
                    break;
                case 'restore' :  // 从回收站还原
                    $data['status'] = 1;
                    break;
                default:
                    return $this->error('参数错误');
                    break;
            }
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
        $post_data = $data ? $data : request()->post();
        try {
            if(empty($post_data[$this->pk])){
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