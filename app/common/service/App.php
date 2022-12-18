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
use app\admin\service\Database as DatabaseService;
use think\facade\Db;

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

    static function getApp($name = ''){
        return AppM::where('name', $name)->find();
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
}