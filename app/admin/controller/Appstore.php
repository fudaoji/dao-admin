<?php
/**
 * Created by PhpStorm.
 * Script Name: Appstore.php
 * Create: 2023/1/17 13:39
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\controller;

use app\common\service\DACommunity;
use app\AdminController;
use GuzzleHttp\Client;

class Appstore extends AdminController
{
    public function __construct(){
        parent::__construct();
    }

    /**
     * 下载应用
     * @return \support\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function download()
    {
        $post_data = input('post.');
        $params = [
            'id' => $post_data['app_id']
        ];

        if(is_string($res = DACommunity::appDownload($params))){
            return $this->error($res);
        }

        $app = $res['app'];
        $app_install_path = plugin_path($app['name']);

        if (file_exists($app_install_path))
            return $this->error($app['name'] . '目录已经存在或者您已经安装过【' . $app['title'] . '】，如果您要重新安装，请先卸载此应用');

        $tem_file = runtime_path() . $app['name'].$app['version'].'-'.time(). '.tmp';
        $response = (new Client())->post($app['package']);
        if($response->getStatusCode() === 200){
            $package = $response->getBody()->getContents();
            file_put_contents($tem_file, $package);
        }else{
            return  $this->error('下载安装包出错: ' . $response->getStatusCode());
        }

        $zip = new \ZipArchive;
        $res = $zip->open($tem_file);
        if ($res === true) {
            $zip->extractTo(plugin_path(''));
            $zip->close();
            @unlink($tem_file); //删除临时压缩包
            return $this->success('下载成功，正在跳转安装界面。。。', url('admin/app/uninstallList'));
        } else {
            return $this->error('解压失败，请检查是否有写入权限!');
        }
    }

    /**
     * 应用详情
     * @return mixed|\support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function appInfo()
    {
        $params = [
            'id' => input('id', 0)
        ];
        if(is_string($res = DACommunity::getAppInfo($params))){
            return $this->error($res);
        }
        if(is_string($cates = DACommunity::getAppCates())){
            return $this->error($cates);
        }

        $this->assign('cates', array_merge(['全部' => '全部'], $cates['cates']));
        $this->assign('types', array_merge(['all' => '全部'], $cates['types']));
        $info = $res['info'];
        $info['type'] = explode(',', $info['type']);

        $this->assign('info', $info);
        $this->assign('upgrade_list', $res['upgrade_list']);
        return $this->show();
    }

    /**
     * 应用列表
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function index()
    {
        $type = input('type', 'all');
        $search_key = input('search_key', '');
        $cate = input('cate', '全部');
        $current_page = input('page', 1);
        $page_size = 12;
        $params = [
            'type' => $type,
            'cate' => $cate,
            'search_key' => input('search_key', ''),
            'current_page' => $current_page,
            'page_size' => $page_size
        ];

        if(is_string($res=DACommunity::getApps($params))){
            return $this->error($res);
        }
        if(is_string($cates = DACommunity::getAppCates())){
            return $this->error($cates);
        }

        $this->assign('cates', array_merge(['全部' => '全部'], $cates['cates']));
        $this->assign('types', array_merge(['all' => '全部'], $cates['types']));
        $this->assign('total',  $res['total']);
        $this->assign('apps', $res['list']);
        $this->assign('user_addon', $res['user_addon']);
        $this->assign('page_size', $page_size);
        $this->assign('search_key', $search_key);
        $this->assign('page', $current_page);
        $this->assign('type', $type);
        $this->assign('cate', $cate);
        $this->assign('user', DACommunity::getUserInfo());
        return $this->show();
    }

    public function upgrade()
    {
        if (!DACommunity::checkLogin())
            return $this->error('请先登录官方社区!', url('login'));

        if (request()->isPost()) {
            $post_data = input('post.');
            $addon_path = ADDON_PATH . $post_data['addon'] . DS;
            if (!file_exists($addon_path))
                $this->error($post_data['addon'] . '目录不存在');

            $addon = model('addons')->getOneByMap(['addon' => $post_data['addon']], true, true);

            $res = $this->doRequest(['uri' => self::$apis['getAppUpgradePackage'], 'data' => ['addon' => $post_data['addon'], 'version' => $addon['version']]]);
            if($res['code'] == 1){
                $upgrade = $res['data']['upgrade'];
            }else{
                $this->error($res['msg']);
            }

            $zip = new \ZipArchive;
            //备份
            $back_zip_name = env('runtime_path') . $post_data['addon'] . $addon['version'] . '-backup.zip';
            if (!$zip->open($back_zip_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
                $this->error('无法创建备份压缩包');
            }
            $this->addFileToZip($addon_path, $zip);
            $zip->close();

            $tem_file = env('runtime_path') . $post_data['addon'].$upgrade['version'].'-'.time() . '.tmp';
            $package = http_post($upgrade['upgrade_url'], []);
            file_put_contents($tem_file, $package);

            $res = $zip->open($tem_file);
            if ($res === TRUE) {
                $zip->extractTo(ADDON_PATH);
                $zip->close();
            } else {
                $this->error('解压失败，请检查是否有写入权限');
            }

            if (is_file($addon_path . 'upgrade.sql')) {
                execute_addon_upgrade_sql($addon_path . 'upgrade.sql');

                @unlink($addon_path . 'upgrade.sql');
            }
            @unlink($tem_file);
            model('addons')->updateOne(['id' => $addon['id'], 'version' => $upgrade['version']]);
            $this->success('恭喜您，升级成功');
        }

        $res = $this->doRequest(['uri' => self::$apis['listUpgradeApps']]);
        if($res['code'] == 1){
            $list = $res['data']['list'];
        }else{
            $this->error($res['msg']);
        }

        $data_list = [];
        if (!empty($list)) {
            foreach ($list as $v) {
                $addon = model('addons')->getOneByMap(['addon' => $v['name']], 'name,addon,version,author,logo', true);
                if(!empty($addon) && $v['version'] > $addon['version']){
                    $addon['new_version'] = $v['version'];
                    $addon['update_time'] = $v['update_time'];
                    $data_list[] = $addon;
                }
            }
        }

        return $this->show(['lists' => $data_list]);
    }

    public function show($assign = [], $view = '', $app = null)
    {
        $assign['user'] = DACommunity::getUserInfo();
        $assign['token'] = DACommunity::checkLogin();
        $assign['official_href'] = 'http://daoadmin.kuryun.com';
        return parent::show($assign, $view, $app); // TODO: Change the autogenerated stub
    }
}