<?php
/**
 * SCRIPT_NAME: Admin.php
 * Created by PhpStorm.
 * Time: 2020/9/6 23:23
 * Description: 管理员
 * @author: fudaoji <fdj@kuryun.cn>
 */

namespace app\tenant\controller;

use app\common\model\Tenant as TenantM;
use app\common\model\TenantGroup;
use app\common\model\TenantInfo;
use app\common\model\TenantInfo as TenantInfoM;
use app\TenantController;
use app\common\service\Tenant as TenantService;

class Tenant extends TenantController
{
    /**
     * @var TenantM
     */
    protected $model;
    /**
     * @var TenantInfo
     */
    private $infoM;

    public function __construct(){
        parent::__construct();
        $this->model = new TenantM();
        $this->infoM = new TenantInfo();
    }

    /**
     * 列表
     * Author: Jason<dcq@kuryun.cn>
     */
    public function index(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [
                ['pid', '=', $this->tenantInfo('id')],
                ['group_id', '<>', \app\tenant\service\Auth::getChannelGroup('id')]
            ];
            !empty($post_data['search_key']) && $where[] = ['username|mobile|realname', 'like', '%'.$post_data['search_key'].'%'];

            $total = $this->model->where($where)
                ->count();
            if ($total) {
                $list = $this->model->where($where)
                    ->page($post_data['page'], $post_data['limit'])
                    ->order('id', 'desc')
                    ->alias('tenant')
                    ->leftJoin('tenant_info info','tenant.id = info.id')
                    ->field(['tenant.*','info.cp_limit'])
                    ->select();
            } else {
                $list = [];
            }

            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '账号、手机号、名称']
        ])
            ->addTopButton('addnew')
            ->addTableColumn(['title' => '序号', 'type' => 'index'])
            ->addTableColumn(['title' => '名称', 'field' => 'realname'])
            ->addTableColumn(['title' => '账号', 'field' => 'username'])
            ->addTableColumn(['title' => '手机号', 'field' => 'mobile'])
            ->addTableColumn(['title' => '日发布数量', 'field' => 'cp_limit'])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'enum', 'options' => [0 => '禁用', 1 => '启用']])
            ->addTableColumn(['title' => '操作', 'width' => 220, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('edit', ['title' => '修改密码','class' => 'layui-btn layui-btn-warm layui-btn-xs','href' => url('setpassword', ['id' => '__data_id__'])])
            ->addRightButton('delete');
        return $builder->show();
    }

    /**
     * 编辑
     */
    public function edit(){
        $id = input('id');
        $data = $this->model->alias('tenant')
            ->leftJoin('tenant_info info','tenant.id = info.id')
            ->field(['tenant.*','info.cp_limit'])
            ->find($id);
        if(! $data){
            return $this->error('id参数错误');
        }
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')  //设置页面标题
        ->setPostUrl(url('savepost')) //设置表单提交地址
        ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('realname', 'text', '名称', '名称', [], 'required')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('mobile', 'text', '手机', '手机')
            ->addFormItem('status', 'radio', '状态', '状态', [1 => '启用', 0 => '禁用'])
            ->setFormData($data);

        TenantService::isLeader($this->tenantInfo())
        && $builder->addFormItem('cp_limit', 'number', '日发布数', '日发布数');

        return $builder->show();
    }

    /**
     * 添加
     */
    public function add(){
        $data = [
            'cp_limit' => 0
        ];
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('realname', 'text', '名称', '名称', [], 'required')
            ->addFormItem('username', 'text', '账号', '4-20位', [], 'required minlength="4" maxlength="20"')
            ->addFormItem('password', 'password', '密码', '6-20位', [], 'required')
            ->addFormItem('mobile', 'text', '手机', '手机');
        TenantService::isLeader($this->tenantInfo())
        && $builder->addFormItem('cp_limit', 'number', '日发布数', '日发布数')
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 编辑
     */
    public function info(){
        if(request()->isPost()){
            $post_data = input('post.');
            $post_data['union_cookie'] = str_replace(' ', '', $post_data['union_cookie']);
            if($this->infoM->update($post_data)){
                return $this->success('保存成功!', url('info'));
            }
            return $this->error('未修改数据无需提交!');
        }
        $data = \App\common\service\Tenant::getInfo(['tenant_id' => $this->tenantInfo('id')]);
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('团长账号信息')  //设置页面标题
            ->setPostUrl(url('info')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('union_cookie', 'textarea', '会话cookie', '会话cookie', [], 'required')
            ->addFormItem('app_key', 'text', 'AppKey', '应用appKey', [], 'required')
            ->addFormItem('app_secret', 'text', 'AppSecret', '应用appSecret', [], 'required')
            ->addFormItem('union_id', 'number', 'unionId', '联盟unionId', [], 'required')
            ->setFormData($data);
        return $builder->show();
    }

    /**
     * 编辑
     */
    public function account(){
        $data = $this->model->find($this->tenantInfo('id'));
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('系统账号信息')  //设置页面标题
            ->setPostUrl(url('savepost')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('realname', 'text', '名称', '名称', [], 'required')
            ->addFormItem('mobile', 'text', '手机', '手机', [], 'required')
            ->addFormItem('email', 'text', '邮箱', '邮箱')
            ->setFormData($data);
        return $builder->show();
    }

    /**
     * 修改密码
     */
    public function setPassword(){
        if(request()->isPost()){
            $post_data = input('post.');
            if(empty($post_data['password'])){
                return $this->error('请填写新密码');
            }
            $post_data['password'] = fa_generate_pwd($post_data['password']);
            $res = $this->model->update($post_data);
            if($res){
                return $this->success('操作成功！');
            }else{
                return $this->error('操作失败，请联系管理员');
            }
        }
        $id = input('id');
        $data = $this->model->find($id);
        if(! $data){
            return $this->error('id参数错误');
        }
        unset($data['password']);
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑')  //设置页面标题
            ->setPostUrl(url('setPassword')) //设置表单提交地址
            ->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('password', 'password', '新密码', '6-20位', [], 'required')
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 保存数据
     * @param $request
     * @param string $url
     * @param array $data
     * @return mixed
     * @Author  Doogie<461960962@qq.com>
     */
    public function savePost($request, $url='', $data=[]){
        $post_data = input('post.');
        $post_data['group_id'] = \app\tenant\service\Auth::getChildGroupId();
        $post_data['pid'] = $this->tenantInfo('id');
        $post_data['leader_id'] = $this->tenantInfo('leader_id') ?: $this->tenantInfo('id');
        if(!empty($post_data['password'])){
            $post_data['password'] = fa_generate_pwd($post_data['password']);
        }

        $res = $this->validate($post_data, "Tenant.edit");
        if($res !== true){
            return $this->error($res, '', ['token' => token()]);
        }
        if(TenantService::isLeader($this->tenantInfo())){
            $info_data = [
                'cp_limit' => $post_data['cp_limit']
            ];
            unset($post_data['cp_limit']);
        }
        try {
            if(empty($post_data[$this->pk])){
                $res = $this->model->create($post_data);
            }else {
                $res = $this->model->update($post_data);
            }
            if($res){
                if(!empty($info_data)){
                    $info_data = array_merge($info_data, ['id' => $res['id']]);
                    if($info = $this->infoM->find($res['id'])){
                        $this->infoM->update($info_data);
                    }else{
                        $this->infoM->create($info_data);
                    }
                }
                return $this->success("操作成功!", '');
            }else{
                return $this->error("未修改数据无需提交", null, ['token' => token()]);
            }
        }catch (\Exception $e){
            $msg = $e->getMessage();
            return $this->error($msg, null, ['token' => token()]);
        }
        //return parent::savePost($request, $url, $post_data);
    }

    /**
     * 修改个人密码
     * @return mixed
     * Author: Doogie<fdj@kuryun.cn>
     * @throws \think\Exception
     */
    public function setPersonPw(){
        if(request()->isPost()){
            $post_data = ['password' => input('post.password')];
            if(!empty($post_data['password'])){
                $post_data['password'] = fa_generate_pwd($post_data['password']);
            }
            $post_data['id'] = $this->tenantInfo('id');
            $res = $this->model->update($post_data);
            if($res){
                session([SESSION_TENANT => null]);
                return $this->success('密码修改成功，请重新登录', url('auth/login'));
            }else{
                return $this->error('系统出错');
            }
        }
        //使用FormBuilder快速建立表单页面。
        $builder = new FormBuilder();
        $builder->setMetaTitle('修改个人密码')  //设置页面标题
        ->setPostUrl(url('setPersonPw')) //设置表单提交地址
        ->addFormItem('password', 'password', '新密码', '6-20位', [], 'required minlength=6 maxlength=20');

        return $builder->show();
    }
}