<?php

define('IS_WRITE', false);

/**
 *
 * @throws \Psr\SimpleCache\InvalidArgumentException
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\DbException
 * @throws \think\db\exception\ModelNotFoundException
 * Author: fudaoji<fdj@kuryun.cn>
 */
function lockFile(){
    file_put_contents(base_path() . '/install.lock', 'ok!');
    show_msg("安装完成");
}

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env()
{
    $items = array(
        'os' => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
        'php' => array('PHP版本', '5.6.0+', '5.6+', PHP_VERSION, 'success'),
        'upload' => array('附件上传', '不限制', '2M+', '未知', 'success'),
        'gd' => array('GD库', '2.0', '2.0+', '未知', 'success'),
        'disk' => array('磁盘空间', '30M', '不限制', '未知', 'success'),
    );

    //PHP环境检测
    if ($items['php'][3] < $items['php'][1]) {
        $items['php'][4] = 'error';
        session(['error' => true]);
    }

    //附件上传检测
    if (@ini_get('file_uploads'))
        $items['upload'][3] = ini_get('upload_max_filesize');

    //GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if (empty($tmp['GD Version'])) {
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'error';
        session(['error' => true]);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }
    unset($tmp);

    //磁盘空间检测
    if (function_exists('disk_free_space')) {
        $items['disk'][3] = floor(disk_free_space(base_path()) / (1024 * 1024)) . 'M';
    }

    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile()
{
    $root_path = base_path();
    $items = array(
        array('dir', '可写', 'success', '/'),
        array('dir', '可写', 'success', 'app'),
        array('dir', '可写', 'success', 'config'),
        array('dir', '可写', 'success', 'extend'),
        array('dir', '可写', 'success', 'public/uploads'),
        array('dir', '可写', 'success', 'route'),
        array('dir', '可写', 'success', 'tests'),
        array('dir', '可写', 'success', 'themes'),
        array('dir', '可写', 'success', 'vendor'),
        array('dir', '可写', 'success', 'runtime'),
    );

    foreach ($items as &$val) {
        if('dir' == $val[0]){
            if(!is_writable($root_path . $val[3])) {
                if(is_dir($root_path . $val[3])) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session(['error' => true]);
                } else {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session(['error' => true]);
                }
            }
            if(file_exists($root_path . $val[3])) {
                if(!is_writable($root_path . $val[3])) {
                    $val[1] = '不可写';
                    $val[2] = 'error';
                    session(['error' => true]);
                }
            } else {
                if(!is_writable(dirname($root_path . $val[3]))) {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session(['error' => true]);
                }
            }
        }
    }
    return $items;
}

/**
 * 函数检测 
 * @return array 检测数据
 */
function check_func()
{
    $items = [
        ['pdo', '支持', 'success', '类'],
        ['pdo_mysql', '支持', 'success', '模块'],
        ['file_get_contents', '支持', 'success', '函数'],
        ['mb_strlen', '支持', 'success', '函数'],
        ['exec', '支持', 'success', '函数'],
        ['shell_exec', '支持', 'success', '函数'],
        ['pcntl_fork', '支持', 'success', '函数'],
        ['pcntl_wait', '支持', 'success', '函数'],
        ['pcntl_signal', '支持', 'success', '函数'],
        ['proc_get_status', '支持', 'success', '函数'],
        ['proc_open', '支持', 'success', '函数'],
        ['pcntl_signal_dispatch', '支持', 'success', '函数'],
        ['pcntl_alarm', '支持', 'success', '函数'],
    ];

    foreach ($items as &$val) {
        if (('类' == $val[3] && !class_exists($val[0]))
            || ('模块' == $val[3] && !extension_loaded($val[0]))
            || ('函数' == $val[3] && !function_exists($val[0]))
        ) {
            $val[1] = '不支持';
            $val[2] = 'error';
            session(['error' => true]);
        }
    }

    return $items;
}

/**
 * 写入配置文件
 * @return string
 * Author: fudaoji<fdj@kuryun.cn>
 */
function write_config()
{
    //show_msg('开始写入配置文件...');
    $root_path = base_path() . '/';
    //读取配置内容
    $conf = file_get_contents($root_path . '.env.example');
    //替换cache_type
    $conf = str_replace("[cache_type]", session('cache_type'), $conf);
    //替换cache_prefix
    $conf = str_replace("[cache_prefix]", session('cache_prefix'), $conf);
    //替换sql
    $db = session('db_config');
    foreach ($db as $name => $value) {
        $conf = str_replace("[{$name}]", $value, $conf);
    }
    //替换redis
    if($redis = (array)session('redis_config')){
        foreach ($redis as $name => $value) {
            $conf = str_replace("[{$name}]", $value, $conf);
        }
    }

    //替换memcache
    if($memcache = (array)session('memcache_config')){
        foreach ($memcache as $name => $value) {
            $conf = str_replace("[{$name}]", $value, $conf);
        }
    }

    //写入应用配置文件
    file_put_contents($root_path . '.env', $conf);
    return '';
}

/**
 * 创建数据表
 * @param $db
 * @param string $prefix
 * Author: fudaoji<fdj@kuryun.cn>
 * @return bool
 * @throws \Psr\SimpleCache\InvalidArgumentException
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\DbException
 * @throws \think\db\exception\ModelNotFoundException
 */
function create_tables($db, $prefix = '')
{
    show_msg('开始创建数据表...');
    $install_path = app_path() . '/install/';
    $sql_file = $install_path . 'data/install.sql';
    //读取SQL文件
    if(!is_file($sql_file)){
        session(['error' => true]);
        show_msg($sql_file . ' 数据库文件不存在');
        return false;
    }
    $sql = file_get_contents($sql_file);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    //替换表前缀
    $original = '`__PREFIX__';
    $sql = str_replace($original, "`{$prefix}", $sql);

    //开始安装
    //show_msg('开始安装数据库...');
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) continue;
        if (substr($value, 0, 12) == 'CREATE TABLE') {
           // $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $name='';
            preg_match('|EXISTS `(.*?)`|',$value,$outValue1);
            preg_match('|TABLE `(.*?)`|',$value,$outValue2);
            if(isset($outValue1[1]) && !empty($outValue1[1])){
                $name=$outValue1[1];
            }
            if(isset($outValue2[1]) && !empty($outValue2[1])){
                $name=$outValue2[1];
            }
            $msg = "创建数据表{$name}";
            if (false !== $db->execute($value)) {
                show_msg($msg . '...成功');
            } else {
                show_msg($msg . '...失败！', 'error');
                session(['error' => true]);
            }
        } else {
            $db->execute($value);
        }
    }
    show_msg('数据表导入完成！');
}

/**
 * 注册创始人账号
 * @param $db
 * @param $prefix
 * @param $admin
 * Author: fudaoji<fdj@kuryun.cn>
 * @throws \Psr\SimpleCache\InvalidArgumentException
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\DbException
 * @throws \think\db\exception\ModelNotFoundException
 */
function register_administrator($db, $prefix, $admin)
{
    show_msg('开始注册创始人帐号...');
    $password = fa_generate_pwd($admin['password']);
    $db->table($prefix . 'admin')->insert(['group_id' => 1, 'username' => $admin['username'], 'password' => $password]);
    show_msg('创始人帐号注册完成！');
}

/**
 * 即时显示提示信息
 * @param $msg
 * @param string $class
 * Author: fudaoji<fdj@kuryun.cn>
 * @throws \Psr\SimpleCache\InvalidArgumentException
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\DbException
 * @throws \think\db\exception\ModelNotFoundException
 */
function show_msg($msg, $class = 'primary')
{
    $msg_list = (array)cache('install_msg');
    array_push($msg_list, ['msg' => $msg, 'class' => $class]);
    cache('install_msg', $msg_list);
}

