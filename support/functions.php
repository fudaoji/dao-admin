<?php
/**
 * Created by PhpStorm.
 * Script Name: functions.php
 * Create: 2023/1/13 15:02
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

if (!function_exists('get_plugin_name')) {
    function get_plugin_name($path, $level = 3){
        $path_layer = explode('/', $path);
        return $path_layer[count($path_layer) - 3];
    }
}