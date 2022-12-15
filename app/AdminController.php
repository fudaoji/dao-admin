<?php

namespace app;

use Webman\Http\Request;

class AdminController extends BaseController
{
    /**
     * 数据库实例
     * @var BaseModel
     */
    protected $model;

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

    /**
     * 权限验证类
     * @var object
     */
    public $auth = null;

    protected $pk = 'id';
    protected $captchaKey = 'captchaAdmin';

    /**
     * 构造函数
     */
    public function __construct()
    {
        //只有在启动时执行
        parent::__construct();
    }

    public function adminInfo($key = null){
        $admin = request()->session()->get(SESSION_ADMIN);
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
            $data['status'] = abs(input('val', 0) - 1);
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
            return $this->error($msg,null, ['token' => token()]);
        }
    }
}