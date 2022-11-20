<?php
/**
 * Created by PhpStorm.
 * Script Name: Jd.php
 * Create: 2022/9/28 14:38
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\taglib;

use support\Log;
use think\template\TagLib;

class Jd extends TagLib
{
    /**
     * 定义标签列表
     */
    protected $tags   =  [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'goodslimit'     => ['attr' => 'name,field,options', 'close' => 0], //闭合标签，默认为不闭合
    ];

    /**
     * @param $tag
     * @return string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function tagGoodslimit($tag)
    {
        $activity = $this->tpl->get($tag['name']);
        $options = $this->tpl->get($tag['options']);
        $field = $tag['field'];
        return $activity[$field] == -1 ? '不限' : ($options[$field][$activity[$field]] ?? '-----');
    }
}