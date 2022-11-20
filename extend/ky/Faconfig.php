<?php
/**
 * Created by PhpStorm.
 * Script Name: FaConfig.php
 * Create: 9/19/22 11:46 PM
 * Description: 增加自定义配置
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace ky;

class Faconfig
{
    protected static $config = [];

    /**
     * 判断是否注入配置
     * @return bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function isInit(){
        return !empty(self::$config);
    }

    /**
     * 检测配置是否存在
     * @access public
     * @param  string $name 配置参数名（支持多级配置 .号分割）
     * @return bool
     */
    public static function has(string $name): bool
    {
        if (false === strpos($name, '.') && !isset(self::$config[strtolower($name)])) {
            return false;
        }

        return !is_null(self::get($name));
    }

    /**
     * 获取一级配置
     * @access protected
     * @param  string $name 一级配置名
     * @return array
     */
    protected static function pull(string $name): array
    {
        $name = strtolower($name);

        return self::$config[$name] ?? [];
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string $name    配置参数名（支持多级配置 .号分割）
     * @param  mixed  $default 默认值
     * @return mixed
     */
    public static function get(string $name = null, $default = null)
    {
        // 无参数时获取所有
        if (empty($name)) {
            return self::$config;
        }

        if (false === strpos($name, '.')) {
            return self::pull($name);
        }

        $name    = explode('.', $name);
        $name[0] = strtolower($name[0]);
        $config  = self::$config;

        // 按.拆分成多维数组进行判断
        foreach ($name as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }

        return $config;
    }

    /**
     * 设置配置参数 name为数组则为批量设置
     * @access public
     * @param  array  $config 配置参数
     * @param  string $name 配置名
     * @return array
     */
    public static function set(array $config, string $name = null): array
    {
        if (!empty($name)) {
            if (isset(self::$config[$name])) {
                $result = array_merge(self::$config[$name], $config);
            } else {
                $result = $config;
            }

            self::$config[$name] = $result;
        } else {
            $result = self::$config = array_merge(self::$config, array_change_key_case($config));
        }

        return $result;
    }
}