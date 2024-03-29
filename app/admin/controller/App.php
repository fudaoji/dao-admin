<?php
/**
 * Created by PhpStorm.
 * Script Name: Admingroup.php
 * Create: 2:52 下午
 * Description:
 * Author: Jason<dcq@kuryun.cn>
 */

namespace app\admin\controller;

use app\AdminController;
use app\common\constant\Common;
use app\common\constant\Platform;
use app\common\model\AppCate as AppCateM;
use app\common\model\App as AppM;
use app\common\model\AppInfo;
use app\common\service\App as AppService;
use app\common\service\Appstore as AppstoreService;
use app\common\service\DACommunity;
use app\common\service\File as FileService;
use think\facade\Db;

class App extends AdminController
{
    /**
     * 数据库实例
     * @var AppM
     */
    protected $model;
    /**
     * 数据库实例
     * @var AppCateM
     */
    protected $cateM;
    /**
     * @var AppInfo
     */
    private AppInfo $appInfoM;

    public function __construct(){
        parent::__construct();
        $this->model = new AppM();
        $this->cateM = new AppCateM();
        $this->appInfoM = new AppInfo();
    }

    static function tabList(){
        return [
            'installed' => ['title' => '已安装', 'href' => url('index')],
            'uninstall' => ['title' => '未安装', 'href' => url('uninstallList')]
        ];
    }

    /**
     * 删除应用包
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function removePost()
    {
        if (request()->isPost()) {
            $name = input('name', '');
            if ($name == '') {
                return $this->error('参数错误');
            }
            $path= plugin_path($name);
            if(!file_exists($path)){
                return $this->error($path.'目录不存在');
            }
            if(!is_writable($path)){
                return $this->error($path.'目录没有删除权限');
            }

            if(($res = FileService::delDirRecursively($path, true)) === true){
                if(! config('app.debug')){
                    system_reload();
                }
                return $this->success('安装包删除成功');
            }else{
                return $this->error('删除应用目录失败:' . $res);
            }
        }
    }

    /**
     * 卸载
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function uninstallPost()
    {
        if (request()->isPost()) {
            $name = input('name', '');
            if ($name == '') {
                return $this->error('参数错误');
            }

            if(AppService::clearAppData(['name' => $name])){
                AppService::runUninstall();
                return $this->success('应用卸载成功，数据已删除');
            }else{
                return $this->error('删除应用目录失败');
            }
        }
    }

    /**
     * 安装
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function installPost()
    {
        if (request()->isPost()) {
            $name = input('name', '');
            if ($name == '') {
                return $this->error('name参数错误');
            }

            if ($app = AppService::getApp($name)) {
                return $this->error('应用已安装!');
            }

            $cf = AppService::getAppConfigByFile($name);
            $data = [
                'name' => $cf['name'],
                'title' => $cf['title'],
                'desc' => isset($cf['desc']) ? $cf['desc'] : '',
                'version' => isset($cf['version']) ? $cf['version'] : '',
                'author' => isset($cf['author']) ? $cf['author'] : '',
                'logo' => isset($cf['logo']) ? plugin_logo($cf['logo'], $cf['name']) : '',
                'admin_url' => $cf['admin_url'] ?? '',
                'admin_url_type' => $cf['admin_url_type'] ?? 1,
                'tenant_url' => $cf['tenant_url'] ?? '',
                'tenant_url_type' => $cf['tenant_url_type'] ?? 1,
                'status' => 0,
                'type' => isset($cf['type']) ? $cf['type'] : Platform::MP,
                'create_time' => time(),
                'update_time' => time()
            ];

            $result = $this->validate($data, 'App.add');
            if ($result !== true) {
                return $this->error($result);
            }

            Db::startTrans();
            try {
                $install_sql = plugin_path($name, 'install.sql');
                if (is_file($install_sql) && is_readable($install_sql)) {
                    $res = AppService::executeAppInstallSql($install_sql);
                    if($res !== true){
                        return $this->error($res);
                    }
                }
                //执行应用中的Install::install
                AppService::runInstall($name);

                //入库
                if ($id = $this->model->insertGetId($data)) {
                    $insert = ['id' => $id, 'price' => 0.00, 'old_price' => 0.00];
                    if(is_string($remote_info = DACommunity::getAppInfoByName($name))){
                        return $this->error($remote_info);
                    }else{
                        $insert['detail'] = empty($remote_info['info']['detail']) ? '' : $remote_info['info']['detail'];
                        if(!empty($remote_info['info']['cates'])){
                            $this->model->update(['id' =>$id, 'cates' => $remote_info['info']['cates']]);
                        }
                        $insert['snapshot'] = empty($remote_info['info']['snapshot']) ? '' : implode('|', explode(',', $remote_info['info']['snapshot']));
                    }

                    $this->appInfoM->insert($insert);
                    $extra_msg = ',请先进行应用相关配置后再上架。';
                    //reload system while in product development
                    if(! config('app.debug')){
                        system_reload();
                    }
                }
                Db::commit();
                return $this->success('安装应用成功' . $extra_msg);
            }catch (\Exception $e){
                Db::rollback();
                return $this->error('安装应用失败:' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * 未安装列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function uninstallList(){
        if(request()->isPost()){
            $list = AppService::listUninstallApp();
            $total = count($list);
            foreach ($list as &$item){
                $item['logo'] = '/app/'.$item['name'].'/'.$item['logo'];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setTabNav(self::tabList(), 'uninstall')
            ->addTopButton('addnew', ['text' => '创建应用', 'href' => url('build'), 'class' => 'layui-btn-default'])
            ->addTableColumn(['title' => 'logo', 'field' => 'logo', 'type' => 'picture'])
            ->addTableColumn(['title' => '标识', 'field' => 'name'])
            ->addTableColumn(['title' => '名称', 'field' => 'title'])
            ->addTableColumn(['title' => '版本', 'field' => 'version'])
            ->addTableColumn(['title' => '简介', 'field' => 'desc'])
            ->addTableColumn(['title' => '操作', 'width' => 150, 'type' => 'toolbar'])
            ->addRightButton('self', ['text' => '安装', 'href' => url('installpost', ['name' => '__data_name__']), 'data-ajax' => true, 'data-confirm' => '确认安装吗？'])
            ->addRightButton('delete', ['text' => '删除包', 'href' => url('removepost', ['name' => '__data_name__'])]);
        return $builder->show();
    }

    /**
     * 列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = [];
            !empty($post_data['search_key']) && $where[] = ['app.title|app.name', 'like', '%'.$post_data['search_key'].'%'];
            $query = $this->model->alias('app')
                ->join('app_info info', 'info.id=app.id')
                ->where($where);
            $total = $query->count();
            if($total){
                $list = $query->page($post_data['page'], $post_data['limit'])
                    ->field(['app.*', 'info.sale_num', 'info.sale_num_show','info.price', 'info.old_price'])
                    ->order('sale_num_show', 'desc')
                    ->select();
            }else{
                $list = [];
            }
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setTabNav(self::tabList(), 'installed')
            ->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '应用名称或标识']
        ])
            ->addTopButton('addnew', ['text' => '采购应用', 'href' => url('appstore/index')])
            ->addTopButton('addnew', ['text' => '创建应用', 'href' => url('build'), 'class' => 'layui-btn-default'])
            ->addTableColumn(['title' => 'logo', 'field' => 'logo', 'type' => 'picture'])
            ->addTableColumn(['title' => '标识', 'field' => 'name'])
            ->addTableColumn(['title' => '名称', 'field' => 'title'])
            ->addTableColumn(['title' => '版本', 'field' => 'version'])
            ->addTableColumn(['title' => '价格(元/月)', 'field' => 'price'])
            ->addTableColumn(['title' => '实际销量', 'field' => 'sale_num'])
            ->addTableColumn(['title' => '虚拟销量', 'field' => 'sale_num_show'])
            ->addTableColumn(['title' => '状态', 'field' => 'status', 'type' => 'switch', 'text' => '上架|下架'])
            ->addTableColumn(['title' => '操作', 'width' => 170, 'type' => 'toolbar'])
            ->addRightButton('edit')
            ->addRightButton('edit', ['text' => '后台配置', 'lay-event' => 'appConsole','class' => 'layui-btn-warm'])
            ->addRightButton('delete', ['text' => '卸载', 'href' => url('uninstallpost', ['name' => '__data_name__'])]);
        return $builder->show();
    }

    /**
     * 编辑应用信息
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function edit(){
        if(request()->isPost()){
            $post_data = input('post.');
            unset($post_data['__token__']);
            $this->model->update([
                'id' => $post_data['id'],
                'cates' => $post_data['cates'],
                'version' => $post_data['version'],
                'status' => $post_data['status']
            ]);
            unset($post_data['version'], $post_data['status'], $post_data['cates']);
            $this->appInfoM->update($post_data);
            return $this->success('保存成功');
        }
        if(! $data = $this->model->find(input('id', 0))){
            return $this->error('数据不存在');
        }

        $data = array_merge($data->toArray(), $this->appInfoM->find($data['id'])->toArray());
        $data['cates'] = empty($data['cates']) ? [] : explode(',', $data['cates']);

        $cates = $this->cateM->where('status',1)
            ->column('title');

        $builder = new FormBuilder();
        $builder->addFormItem('id', 'hidden', 'id', 'id')
            ->addFormItem('cates', 'chosen_multi', '分类标签', '可多选', array_combine($cates, $cates), 'required')
            ->addFormItem('version', 'text', '版本', '版本', [], 'required')
            ->addFormItem('sale_num_show', 'number', '虚拟销量', '前台显示的数字', [], 'required min=0')
            ->addFormItem('old_price', 'text', '原价', '原价', [], 'required min="0"')
            ->addFormItem('price', 'text', '售价', '每月的费用', [], 'required min="0"')
            ->addFormItem('snapshot', 'pictures_url', '应用快照', '应用典型界面截图', [], 'required')
            ->addFormItem('detail', 'ueditor', '详细介绍', '详细介绍', [], 'required max=50000')
            ->addFormItem('status', 'radio', '上架状态', '上架状态', Common::goodsStatus(), 'required')
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 创建应用
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function build(){
        if(request()->isPost()){
            $post_data = input('post.');
            if(($res = AppService::buildApp($post_data)) === true){
                if(! config('app.debug')){
                    system_reload();
                }
                return $this->success('创建成功, 请前往安装！', url('uninstallList'));
            }else{
                return $this->error($res);
            }
        }

        $default_data = [
            'version' => '1.0.0',
            'author' => $this->adminInfo('realname')
        ];
        $builder = new FormBuilder();
        $builder->setPostUrl(url('build'))
            ->addFormItem('type', 'chosen_multi', '支持平台', '请选择应用的支持平台', Platform::types(), 'required')
            ->addFormItem('name', 'text', '应用标识', '请输入唯一应用标识，支持小写字母、数字和下划线，且不能以数字开头', [], 'required minlength=2 maxlength=20')
            ->addFormItem('title', 'text', '应用名称', '请输入应用名称，2-50长度', [], 'required minlength=2 maxlength=50')
            ->addFormItem('version', 'text', '应用版本', '例如1.0.0', [], 'required')
            ->addFormItem('logo', 'picture_url', '应用LOGO', '请上传比例为1:1的应用LOGO', [], 'required')
            ->addFormItem('author', 'text', '作者', '应用作者', [], 'required maxlength=100')
            ->addFormItem('desc', 'textarea', '应用描述', '200字内', [], 'maxlength=200')
            ->setFormData($default_data);
        return $builder->show();
    }
}