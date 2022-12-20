<?php

use support\Log;
use think\facade\Cache;
use think\helper\Str;
use ky\Faconfig;
use think\Model;

require_once app_path() . DIRECTORY_SEPARATOR . 'define.php';
if (!function_exists('plugin_config')) {
    /**
     * @param string|null $key
     * @param null $default
     * @param string $plugin
     * @return array|mixed|null
     */
    function plugin_config(string $key = null, $default = null, $plugin = '')
    {
        $plugin = $plugin ? $plugin : request()->plugin;
        if(is_null($key)){
            $key = 'plugin.' . $plugin;
        } else{
            $key = 'plugin.' . $plugin . '.' . $key;
        }
        return  config($key, $default);
    }
}
if (!function_exists('plugin_logo')) {
    /**
     * 应用logo地址
     * @param string $plugin
     * @param string $logo
     * @return string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    function plugin_logo($plugin = '', $logo = '')
    {
        return '/app/'.$plugin.'/'.$logo;
    }
}
if (!function_exists('plugin_path')) {
    /**
     * 插件目录
     * @param string $plugin
     * @param string $file
     * @return string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    function plugin_path($plugin = '', $file = '')
    {
        return base_path() . DIRECTORY_SEPARATOR . 'plugin/' . $plugin . ($file ? DIRECTORY_SEPARATOR . $file : '');
    }
}

if (!function_exists('dao_log')) {

    /**
     * @param string $plugin
     * @param string $handler
     * @return \Monolog\Logger
     * Author: fudaoji<fdj@kuryun.cn>
     */
    function dao_log($plugin = '', $handler = 'default')
    {
        return Log::channel($plugin ? "plugin.{$plugin}.{$handler}" : $handler);
    }
}

if (!function_exists('system_reload')) {
    /**
     * 重载系统
     * return bool
     */
    function system_reload(): bool
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return false;
        }
        if (function_exists('posix_kill')
            && function_exists('posix_getppid')) {
            posix_kill(posix_getppid(), SIGUSR1);
            return true;
        }
        return false;
    }
}

if(! function_exists('generate_token')) {
    function generate_token($len) {
        $header = request()->header();
        $data = $header['user-agent'] . $header['host']
            .time() . rand();
        return md5(time().$data);
    }
}

if (!function_exists('cut_str')) {
    /**
     * 显示指定长度的字符串，超出长度以省略号(...)填补尾部显示
     * @param $str
     * @param int $len
     * @param string $suffix
     * @return string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    function cut_str($str, $len = 30, $suffix = '...')
    {
        if (strlen($str) > $len) {
            $str = mb_substr($str, 0, $len) . $suffix;
        }
        return $str;
    }
}

if (!function_exists('jd_cdn')) {
    function jd_cdn($name = '')
    {
        return 'https://img14.360buyimg.com/n1/' . $name;
    }
}

if (!function_exists('dao_money_format')) {
    /**
     * 金额快速格式化
     * @param float $decimal
     * @return string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    function dao_money_format($decimal = 0.0)
    {
        return number_format($decimal, 2, '.', '');
    }
}

if (!function_exists('halt')) {
    /**
     * 调试变量并且中断输出
     * @param mixed $vars 调试变量或者信息
     * @throws Exception
     */
    function halt(...$vars)
    {
        throw new \Exception(...$vars);
    }
}

if(! function_exists('fa_generate_pwd')){
    /**
     * hash密码加密
     * @param $password
     * @return bool|string
     * @author: fudaoji<fdj@kuryun.cn>
     */
    function fa_generate_pwd($password)
    {
        $options['cost']  = 10;
        return password_hash($password, PASSWORD_DEFAULT, $options);
    }
}

if (!function_exists('select_list_as_tree')) {
    /**
     * 获取所有数据并转换成一维数组
     * @param Model $model
     * @param array $where
     * @param null $extra
     * @param string $key
     * @param array $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: fudaoji<fdj@kuryun.cn>
     */
    function select_list_as_tree($model, $where = [], $extra = null, $key = 'id', $order = ['sort', 'asc'])
    {
        //获取列表
        $con = [['status', '=', 1]];
        if ($where) {
            $con[] = $where;
        }

        $list = $model->where($con)
            ->order($order[0], $order[1])
            ->select();

        $result = [];
        if ($extra) {
            $result[0] = $extra;
        }
        if ($list) {
            //转换成树状列表(非严格模式)
            $list = \ky\Tree::toFormatTree($list, 'title', 'id', 'pid', 0, false);
            //转换成一维数组
            foreach ($list as $val) {
                $result[$val[$key]] = $val['title_show'];
            }
        }
        return $result;
    }
}

if (!function_exists('cache')) {
    /**
     * 全局缓存控制函数
     * @param string|null $name
     * @param null $options
     * @param null $tag
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function cache(string $name = null, $value = '', $options = null, $tag = null)
    {
        if (is_null($name)) {
            return [];
        }

        if ('' === $value) {
            // 获取缓存
            return 0 === strpos($name, '?') ? Cache::has(substr($name, 1)) : Cache::get($name);
        } elseif (is_null($value)) {
            // 删除缓存
            return Cache::delete($name);
        }

        // 缓存数据
        if (is_array($options)) {
            $expire = $options['expire'] ?? null;
        } else {
            $expire = $options;
        }

        if (is_null($tag)) {
            return Cache::set($name, $value, $expire);
        } else {
            return Cache::tag($tag)->set($name, $value, $expire);
        }
    }
}

if (!function_exists('dao_config')) {
    /**
     * @param string $name
     * @param null $value
     * @return array|mixed|null
     */
    function dao_config(string $name = '', $value = null)
    {
        if (is_array($name)) {
            return Faconfig::set($name, $value);
        }

        return 0 === strpos($name, '?') ? Faconfig::has(substr($name, 1)) : Faconfig::get($name, $value);
    }
}

if (!function_exists('url')) {
    /**
     * 生成URL地址
     * @param string $url
     * @param array $vars
     * @param string $app
     * @param string $plugin
     * @return string
     */
    function url(string $url, array $vars = [], string $app = '', $plugin = ''): string
    {
        $app = $app ?: request()->app;
        $plugin = $plugin ?: request()->plugin;
        $url = trim($url, '/');
        $path_arr = explode('/', $url);
        switch (count($path_arr)){
            case 3:
                $app = $path_arr[0];
                $action = $path_arr[2];
                $controller = $path_arr[1];
                break;
            case 2:
                $action = $path_arr[1];
                $controller = $path_arr[0];
                break;
            default:
                $action = $url;
                $controller_layer = explode('/', request()->getController());
                $controller = $controller_layer[count($controller_layer) - 1];
        }

        $url = $controller . '/' .$action;
        $vars = !empty($vars) ? '?' . http_build_query($vars) : '';

        if (!Str::startsWith($url, '/')) {
            $url = DIRECTORY_SEPARATOR . $url;
        }
        $plugin = $plugin ? ('app/' . $plugin . '/') : '';
        return '/' . $plugin . $app . $url . $vars;
    }
}

if (!function_exists('cookie')) {
    /**
     * cookie快捷操作
     * @param null $name
     * @param null $default
     * @return array|string|null
     * Author: fudaoji<fdj@kuryun.cn>
     */
    function cookie($name = null, $default = null)
    {
        return \request()->cookie($name, $default);
    }
}

if (!function_exists('token')) {
    /**
     * 获取Token令牌
     * @param string $name 令牌名称
     * @param mixed $type 令牌生成方法
     * @return string
     */
    function token(string $name = '__token__', string $type = 'md5'): string
    {
        try {
            return \request()->buildToken($name, $type);
        } catch (\Psr\SimpleCache\InvalidArgumentException $e) {
        }
        return '';
    }
}

if (!function_exists('token_field')) {
    /**
     * 生成令牌隐藏表单
     * @param string $name 令牌名称
     * @param mixed $type 令牌生成方法
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    function token_field(string $name = '__token__', string $type = 'md5'): string
    {
        $token = \request()->buildToken($name, $type);
        return '<input type="hidden" name="' . $name . '" value="' . $token . '" />';
    }
}

if (!function_exists('input')) {
    /**
     * 过滤函数
     * @param string $key
     * @param null $default
     * @return mixed
     */
    function input(string $key = '', $default = null)
    {
        if(empty($key)){
            return \request()->get() + \request()->post();
        }elseif (strpos($key, '.') !== false){
            list($method, $k) = explode('.', $key);
            $method = strtolower($method);
            if(in_array($method, ['get', 'post'])){
                return \request()->$method($k ?: null);
            }
        }
        return \request()->input($key, $default);
    }
}

if (!function_exists('dao_trans')) {
    /**
     * 全局多语言函数
     * @param $str
     * @param array $parameters
     * @param null $domain
     * @param null $locale
     * @return string
     */
    function dao_trans($str, $parameters = [], $domain = null, $locale = null)
    {
        $lang = session('lang', 'zh-CN');
        if (is_numeric($str) || strstr($lang, 'zh-CN')) {
            return $str;
        }
        return trans($str, $parameters, $domain, $locale);
    }
}