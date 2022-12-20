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
use think\facade\Db;
use app\common\model\TenantApp as TenantAppM;

class App extends Common
{
    /**
     * 清空应用数据
     * @param array $params
     * @return bool|mixed
     * Author: fudaoji<fdj@kuryun.cn>
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
        $sql = file_get_contents($sql_path);
        $sql = str_replace("\r", "\n", $sql);
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
        return AppM::where('name', $name)->find();
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
     * 应用购买核销
     * @param array $params
     * Author: fudaoji<fdj@kuryun.cn>
     * @return bool
     */
    static function afterBuyApp($params = []){
        Db::startTrans();
        try {
            $app = $params['app'];
            $tenant_app = TenantAppM::where('company_id', $params['company_id'])
                ->where('app_name', $app['name'])
                ->find();
            if(empty($tenant_app)){
                TenantAppM::create([
                    'company_id' => $params['company_id'],
                    'app_name' => $app['name'],
                    'deadline' => strtotime("+1 year", time())
                ]);
            }else{
                TenantAppM::update([
                    'id' => $tenant_app['id'],
                    'deadline' => strtotime("+1 year", max($tenant_app['deadline'], time()))
                ]);
            }

            AppInfoM::update([
                'id' => $app['id'],
                'sale_num' => $app['sale_num'] + 1,
                'sale_num_show' => $app['sale_num_show'] + 1
            ]);
            Db::commit();
            $res = true;
        }catch (\Exception $e){
            dao_log()->error($e->getMessage());
            Db::rollback();
            $res = false;
        }
        return $res;
    }
}