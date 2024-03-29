<?php
/**
 * Created by PhpStorm.
 * Script Name: App.php
 * Create: 2022/12/15 18:06
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;
use app\common\model\App as AppM;
use app\common\model\AppInfo as AppInfoM;
use app\common\model\AppCate as AppCateM;
use app\admin\service\Database as DatabaseService;
use app\common\service\File as FileService;
use think\facade\Db;
use app\common\model\TenantApp as TenantAppM;

class App extends Common
{
    /**
     * 执行应用中的Install::update
     * @param string $name
     * Author: fudaoji<fdj@kuryun.cn>
     * @param string $from_version
     * @param string $to_version
     */
    static function runUpdate($name = '', $from_version = '', $to_version = ''){
        if(is_file(plugin_path($name, 'Install.php'))){
            $class = "\\plugin\\$name\\Install";
            $install_handler = new $class();
            if(method_exists($install_handler, 'update')){
                $install_handler->update($from_version, $to_version);
            }
        }
    }

    /**
     * 执行应用中的Install::install
     * @param string $name
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function runInstall($name = ''){
        if(is_file(plugin_path($name, 'Install.php'))){
            $class = "\\plugin\\$name\\Install";
            $install_handler = new $class();
            if(method_exists($install_handler, 'install')){
                $install_handler->install();
            }
        }
    }

    /**
     * 执行应用中的Install::uninstall
     * @param string $name
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function runUninstall($name = ''){
        if(is_file(plugin_path($name, 'Install.php'))){
            $class = "\plugin\{$name}\uninstall";
            $install_handler = new $class();
            if(method_exists($install_handler, 'uninstall')){
                $install_handler->uninstall();
            }
        }
    }

    /**
     * 清空应用数据
     * @param array $params
     * @return bool|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    static function clearAppData($params = []){
        $name = $params['name'];
        $app = self::getApp($name);
        Db::startTrans();
        try {
            $install_sql = plugin_path($name, 'install.sql');
            if (is_readable($install_sql)) {
                $sql = file_get_contents($install_sql);
                $sql = str_replace("\r", "\n", $sql);
                $sql = explode(";\n", $sql);
                $original = '`__PREFIX__';
                $prefix = '`'.DatabaseService::getTablePrefix();
                $sql = str_replace("{$original}", "{$prefix}", $sql);

                foreach ($sql as $value) {
                    $value = trim($value);
                    if (!empty($value)) {
                        if (strpos($value, 'CREATE TABLE') !== false) {
                            $table_name = '';
                            preg_match('|EXISTS `(.*?)`|', $value, $table_name1);
                            preg_match('|TABLE `(.*?)`|', $value, $table_name2);

                            !empty($table_name1[1]) && $table_name = $table_name1[1];
                            empty($table_name) && !empty($table_name2[1]) && $table_name = $table_name2[1];
                            if ($table_name) {//如果存在表名
                                Db::execute("DROP TABLE IF EXISTS `{$table_name}`;"); //删除数据库中存在着表，
                            }
                        }
                    }
                }
            }

            AppM::where('id', $app['id'])->delete();
            AppInfoM::where('id', $app['id'])->delete();
            Db::commit();
            $res = true;
        }catch (\Exception $e){
            Db::rollback();
            dao_log()->error($e->getMessage());
            $res = false;
        }
        return $res;
    }

    /**
     * 执行安装语句
     * @param string $sql_path
     * @return bool|string
     * @throws \think\db\exception\BindParamException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function executeAppInstallSql($sql_path = '')
    {
        $sql = trim(file_get_contents($sql_path));
        $sql = preg_replace('/\/\*[\s\S]*?\*\/|--.*?[\r\n]/m', '', $sql);
        $sql = str_replace("\r", "\n", $sql);
        if(empty($sql)){
            return  true;
        }
        $sql = explode(";\n", $sql);
        $original = '`__PREFIX__';
        $prefix = '`'.DatabaseService::getTablePrefix();
        $sql = str_replace("{$original}", "{$prefix}", $sql); //替换掉表前缀

        foreach ($sql as $k => $value) {
            $value = trim($value, "\n");
            if(stripos($value, 'drop') !== false){
                return 'SQL语句包含了DROP TABLE类似的语句';
            }
            if (!empty($value)) {
                if (strpos($value, 'CREATE TABLE') !== false) {
                    $name = '';
                    preg_match('|EXISTS `(.*?)`|', $value, $table_name1);
                    preg_match('|TABLE `(.*?)`|', $value, $table_name2);

                    !empty($table_name1[1]) && $name = $table_name1[1];
                    empty($name) && !empty($table_name2[1]) && $name = $table_name2[1];
                    if (empty($name)) {
                        return ($name . ' SQL语句有误，获取不到表名');
                    }
                    $res = Db::query("SHOW TABLES LIKE '{$name}'");
                    if ($res) {
                        return ($name . '表已经存在，请先手动卸载');
                    }
                }
                $sql[$k] = $value;
            }
        }

        foreach ($sql as $value) {
            if (empty($value) || strlen($value) < 2) {
                continue;
            }
            Db::execute($value);
        }
        return true;
    }

    /**
     * 获取应用基本信息
     * @param string $name
     * @return array|mixed|\think\db\Query|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getApp($name = ''){
        if(intval($name)){
            $where = [['id','=', $name]];
        }else{
            $where = [['name','=', $name]];
        }
        return AppM::where($where)->find();
    }

    /**
     * 获取应用完整信息
     * @param string|int $name
     * @return array|mixed|\think\db\Query|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getAppInfo($name){
        if(intval($name)){
            $where = [['a.id','=', $name]];
        }else{
            $where = [['a.name','=', $name]];
        }
        return AppM::where($where)
            ->alias('a')
            ->join('app_info ai', 'ai.id=a.id')
            ->field(['a.*', 'ai.detail','ai.sale_num','ai.sale_num_show','ai.price','ai.old_price','ai.snapshot','ai.config'])
            ->find();
    }

    /**
     * 获取未安装应用
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function listUninstallApp(){
        $app_folder = opendir(plugin_path());
        $apps = []; //存放未安装的插件
        if ($app_folder) {
            while (($file = readdir($app_folder)) !== false) {
                if ($file != '.' && $file != '..') {
                    if ($app_local = self::getAppConfigByFile($file)) {
                        if (empty(self::getApp($file))) {
                            $apps[] = $app_local;
                        }
                    }
                }
            }
        }
        return $apps;
    }

    /**
     * 获取app配置
     * @param string $name
     * @return array|bool|mixed|null
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getAppConfigByFile($name = ''){
        if (empty($name)) {
            return false;
        }

        return config('plugin.' . $name . '.' . $name);
    }

    /**
     * 获取应用列表字典
     * @param null $where
     * @param string $key
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getAppDict($where = null, $key = 'name')
    {
        if(is_null($where)){
            $where = [['status', '=', 1]];
        }
        return AppM::where($where)->column('title', $key);
    }

    /**
     * 获取应用分类列表字典
     * @param null $where
     * @param string $key
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function getAppCateDict($where = null)
    {
        if(is_null($where)){
            $where = [['status', '=', 1]];
        }
        return AppCateM::where($where)->column('title');
    }

    /**
     * 应用采购核销
     * @param array $params
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function afterBuyApp($params = []){
        $app = $params['app'];
        if(is_int($app) || is_string($app)){
            $app = self::getApp($app);
        }
        $order = $params['order'];
        $tenant_app = TenantAppM::where('company_id', $params['company_id'])
            ->where('app_name', $app['name'])
            ->find();
        if(empty($tenant_app)){
            TenantAppM::create([
                'company_id' => $params['company_id'],
                'app_name' => $app['name'],
                'deadline' => strtotime("+{$order['month']} month", time())
            ]);
        }else{
            TenantAppM::update([
                'id' => $tenant_app['id'],
                'deadline' => strtotime("+{$order['month']} month", max($tenant_app['deadline'], time()))
            ]);
        }

        AppInfoM::update([
            'id' => $app['id'],
            'sale_num' => $app['sale_num'] + 1,
            'sale_num_show' => $app['sale_num_show'] + 1
        ]);
        return true;
    }

    /**
     * 修改app扩展信息
     * @param array $update
     * @return array
     * @throws \think\db\exception\DbException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function updateInfo(array $update)
    {
        if(! empty($update['config']) && is_array($update['config'])){
            $update['config'] = json_encode($update['config'], JSON_UNESCAPED_UNICODE);
        }
        AppInfoM::update($update);
        return $update;
    }

    /**
     * 应用退单
     * @param array $params
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function afterRefundApp($params = []){
        $app = $params['app'];
        if(is_int($app) || is_string($app)){
            $app = self::getApp($app);
        }
        $order = $params['order'];
        $tenant_app = TenantAppM::where('company_id', $params['company_id'])
            ->where('app_name', $app['name'])
            ->find();
        if($tenant_app){
            TenantAppM::update([
                'id' => $tenant_app['id'],
                'deadline' => max(0, strtotime("-{$order['month']} month", $tenant_app['deadline']))
            ]);
        }

        AppInfoM::update([
            'id' => $app['id'],
            'sale_num' => $app['sale_num'] - 1
        ]);
        return true;
    }

    /**
     * 快速创建应用
     * @param array $params
     * @return bool|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function buildApp($params = []){
        $addon_type = strtolower($params['type']);
        $addon_name = strtolower($params['name']);
        $addon_title = $params['title'];
        $addon_version = $params['version'];
        $addon_author = $params['author'];
        $addon_desc = $params['desc'];
        $addon_logo = $params['logo'];
        $addon_path = plugin_path($addon_name);

        try {
            if(file_exists($addon_path)){
                return "应用{$addon_name}已存在";
            }
            $pattern = '/^([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$/';
            if (! preg_match($pattern, $addon_name)) {
                return '应用名不合法。 应用名称只支持小写字母、数字和下划线，且不能以数字开头！';
            }

            //1、解压应用模板
            $plugin_template = plugin_path('__plugin__.zip');
            if(! file_exists($plugin_template)){
                return $plugin_template . "不存在";
            }
            $zip = new \ZipArchive;
            $res = $zip->open($plugin_template);
            if ($res === true) {
                $zip->extractTo($addon_path);
                $zip->close();
            } else {
                return  "解压".$plugin_template."失败，请检查是否有写入权限!";
            }
            $logo_name = 'logo.png';
            file_put_contents(plugin_path($addon_name, 'public'.DS.$logo_name), file_get_contents($addon_logo));

            //rename config/__PLUGIN_NAME__
            if(is_string($res = FileService::renameFile(plugin_path($addon_name, 'config'.DS.'__PLUGIN_NAME__.php'), plugin_path($addon_name, 'config'.DS.$addon_name.'.php')))){
                return $res;
            }

            //2、批量替换应用信息参数
            if(($res = replace_in_files(plugin_path($addon_name),
                    ['__PLUGIN_TYPE__', '__PLUGIN_NAME__', '__PLUGIN_TITLE__','__PLUGIN_DESC__', '__PLUGIN_VERSION__', '__PLUGIN_AUTHOR__', '__PLUGIN_LOGO__'],
                    [$addon_type, $addon_name, $addon_title, $addon_desc, $addon_version, $addon_author, $logo_name],
                    ['public']
                )) !== true){
                return $res;
            }
            return true;
        }catch (\Exception $e){
            @unlink($addon_path);
            return '应用创建错误：' . (string)$e->getMessage();
        }
    }
}