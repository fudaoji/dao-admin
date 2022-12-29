<?php
/**
 * Created by PhpStorm.
 * Script Name: ListBuilder.php
 * Create: 12/28/22 4:39 PM
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\taglib;
use think\template\TagLib;

class ListBuilder extends TagLib
{

    /**
     * 定义标签列表
     */
    protected $tags   =  [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'rightbuttons'     => ['close' => 0], //闭合标签，默认为不闭合
    ];

    /**
     * @return string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function tagRightbuttons()
    {
        return '
{notempty name="right_button_list"}
        <script type="text/html" id="right-btns">
            {if count($right_button_list) > 2}
            <div class="layui-row table-ops">
                <div class="layui-col-xs8 text-right">
                    <div class="layui-btn-group">
                        {foreach $right_button_list as $k => $button}
                        {if $k < 2}
                        <button type="button" {$button[\'attribute\']|raw} >{$button[\'text\']??$button[\'title\']}</button>&nbsp;
                        {/if}
                        {/foreach}
                    </div>
                </div>
                <div class="layui-col-xs4 text-left">
                    <ul class="layui-menu">
                        <li class="layui-menu-item-group layui-menu-item-up">
                            <div class="layui-menu-body-title layui-btn layui-btn-xs layui-btn-primary">
                                更多<i class="layui-icon layui-icon-down"></i>
                            </div>
                            <ul>
                                {foreach $right_button_list as $k => $button}
                                {if $k > 1}
                                <li><button type="button" {$button[\'attribute\']|raw} >{$button[\'text\']??$button[\'title\']}</button></li>
                                {/if}
                                {/foreach}
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            {else/}
            <div class="layui-btn-group">
                {foreach $right_button_list as $button}
                <button type="button" {$button[\'attribute\']|raw} >{$button[\'text\']??$button[\'title\']}</button>
                {/foreach}
            </div>
            {/if}
        </script>
        {/notempty}
';
    }

}