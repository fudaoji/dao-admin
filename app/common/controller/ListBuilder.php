<?php
/**
 * SCRIPT_NAME: ListBuilder.php
 * Created by PhpStorm.
 * Time: 2020/9/27 23:23
 * Description: 列表生成器
 * @author: Doogie <461960962@qq.com>
 */

namespace app\common\controller;

use app\BaseController;

class ListBuilder extends BaseController
{
    private $_meta_title;                  // 页面标题
    private $_top_button_list = [];   // 顶部工具栏按钮组
    private $_search  = [];           // 搜索参数配置
    private $_tab_nav = [];           // 页面Tab导航
    private $_table_column_list = []; // 表格标题字段
    private $_table_data_list   = []; // 表格数据列表
    private $_table_data_list_key = 'id';  // 表格数据列表主键字段名
    private $_table_data_page;             // 表格数据分页
    private $_right_button_list = []; // 表格右侧操作按钮组
    private $_alter_data_list = [];   // 表格数据列表重新修改的项目
    private $_extra_html;                  // 额外功能代码
    private $_template;                    // 模版
    private $_tip;                          //表格顶部的提示框
    private $_data_url = '';                     //数据请求url
    private $_auth = ['super' => 1, 'auth_list' => []];                     //权限列表
    private $_css = '';
    private $_js = '';

    public function __construct() {
        parent::__construct();
        $this->_template = 'builder/list';
    }

    /**
     * 设置额外JS
     * @param string $js
     * @return $this
     * Author: Doogie<fdj@kuryun.cn>
     */
    public function setJs($js = ''){
        $this->_js = $js;
        return $this;
    }

    /**
     * 设置额外样式
     * @param string $css
     * @return $this
     * Author: Doogie<fdj@kuryun.cn>
     */
    public function setCss($css = ''){
        $this->_css = $css;
        return $this;
    }

    /**
     * 设置权限列表
     * @param array $auth
     * @return $this
     * Author: Doogie<fdj@kuryun.cn>
     */
    public function setAuth($auth = []){
        $this->_auth = $auth;
        return $this;
    }
    /**
     * 请求数据URL
     * @param null $url
     * @return $this
     * Author: Doogie<fdj@kuryun.cn>
     */
    public function setDataUrl($url = null){
        $this->_data_url = $url;
        return $this;
    }

    /**
     * 页面提示
     * @param null $tip
     * @return $this
     * Author: Doogie<fdj@kuryun.cn>
     */
    public function setTip($tip = null){
        $this->_tip = $tip;
        return $this;
    }

    /**
     * 设置页面标题
     * @param string $meta_title  标题文本
     * @return object  $this
     * @author Doogie <461960962@qq.com>
     */
    public function setMetaTitle($meta_title) {
        $this->_meta_title = $meta_title;
        return $this;
    }

    /**
     * 加入一个列表顶部工具栏按钮
     * 在使用预置的几种按钮时，比如我想改变新增按钮的名称
     * 那么只需要$builder->addTopButton('add', array('title' => '换个马甲'))
     * 如果想改变地址甚至新增一个属性用上面类似的定义方法
     * @param string $type 按钮类型，主要有add/resume/forbid/recycle/restore/delete/self七几种取值
     * @param array  $attribute 按钮属性，一个定了标题/链接/CSS类名等的属性描述数组
     * @return $this
     * @author Doogie <461960962@qq.com>
     */
    public function addTopButton($type, $attribute = null) {
        $set_status_url = 'setstatus';
        $my_attribute = ['class' => ''] ;
        switch ($type) {
            case 'addnew':  // 添加新增按钮
                // 预定义按钮属性以简化使用
                $my_attribute['text'] = dao_trans('新增');
                $my_attribute['class'] = 'layui-btn-normal';
                $my_attribute['lay-event']  = 'add';
                $my_attribute['href']  = url('add');

                /**
                * 如果定义了属性数组则与默认的进行合并
                * 用户定义的同名数组元素会覆盖默认的值
                * 比如$builder->addTopButton('add', array('title' => '换个马甲'))
                */
                if ($attribute && is_array($attribute)) {
                    $my_attribute = array_merge($my_attribute, $attribute);
                }
                break;
            case 'delete': // 添加删除按钮(我没有反操作，删除了就没有了，就真的找不回来了)
                // 预定义按钮属性以简化使用
                $my_attribute['text'] = dao_trans('删除');
                $my_attribute['class'] = 'layui-btn-danger ';
                $my_attribute['lay-event']  = 'delete';
                $my_attribute['href']  = url($set_status_url, ['status' => 'delete']);

                // 如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的新增按钮
                if ($attribute && is_array($attribute)) {
                    $my_attribute = array_merge($my_attribute, $attribute);
                }
                break;
            case 'resume':  // 添加启用按钮(禁用的反操作)
                //预定义按钮属性以简化使用
                $my_attribute['text'] = dao_trans('批量启用');
                $my_attribute['lay-event']  = 'resume';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['class'] = ' data-ajax data-confirm';
                $my_attribute['href']  = url($set_status_url, ['status' => 'resume']);

                // 如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的新增按钮
                if ($attribute && is_array($attribute)) {
                    $my_attribute = array_merge($my_attribute, $attribute);
                }
                break;
            case 'forbid':  // 添加禁用按钮(启用的反操作)
                // 预定义按钮属性以简化使用
                $my_attribute['lay-event']  = 'forbid';
                $my_attribute['text'] = dao_trans('批量禁用');
                $my_attribute['target-form'] = 'ids';
                $my_attribute['class'] = 'layui-btn-warm data-ajax data-confirm';
                $my_attribute['href']  = url($set_status_url, ['status' => 'forbid']);

                // 如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的新增按钮
                if ($attribute && is_array($attribute)) {
                    $my_attribute = array_merge($my_attribute, $attribute);
                }
                break;
            case 'self': //添加自定义按钮(第一原则使用上面预设的按钮，如果有特殊需求不能满足则使用此自定义按钮方法)
                // 预定义按钮属性以简化使用
                $my_attribute['lay-event'] = 'self';
                $my_attribute['text'] = dao_trans('自定义按钮');

                // 如果定义了属性数组则与默认的进行合并
                if ($attribute && is_array($attribute)) {
                    $my_attribute = array_merge($my_attribute, $attribute);
                }
                break;
        }
        empty($my_attribute['title']) && $my_attribute['title'] = $my_attribute['text'];
        $my_attribute['class'] .= ' layui-btn layui-btn-sm ';
        $node = empty($my_attribute['href']) ? '' : explode('?', $my_attribute['href'])[0];
        ($this->_auth['super'] || in_array($node, $this->_auth['auth_list'])) && $this->_top_button_list[] = $my_attribute;
        return $this;
    }

    /**
     * 设置搜索参数
     * @param $inputs
     * @return $this
     * @author Doogie <461960962@qq.com>
     */
    public function setSearch($inputs=[]) {
        $this->_search = $inputs;
        return $this;
    }

    /**
     * 设置Tab按钮列表
     * @param $tab_list Tab列表  array(
     *                               'title' => '标题',
     *                               'href' => 'http://www.corethink.cn'
     *                           )
     * @param $current_tab 当前tab
     * @return $this
     * @author Doogie <461960962@qq.com>
     */
    public function setTabNav($tab_list, $current_tab) {
        $this->_tab_nav = [
            'tab_list' => $tab_list,
            'current_tab' => $current_tab
        ];
        return $this;
    }

    /**
     * 加一个表格标题字段
     * @param null  $column  列属性,对应layui-table的cols的属性['title'=>'',field:'',width:]
     * @return  $this
     * @author Doogie <461960962@qq.com>
     */
    public function addTableColumn($column = null) {
        $this->_table_column_list[] = $column;
        return $this;
    }

    /**
     * 表格数据列表
     * @param array $table_data_list
     * @return object $this
     * @author Doogie <461960962@qq.com>
     */
    public function setTableDataList($table_data_list) {
        $this->_table_data_list = $table_data_list;
        return $this;
    }

    /**
     * 表格数据列表的主键名称
     * @param mixed $table_data_list_key
     * @return object
     * @author Doogie <461960962@qq.com>
     */
    public function setTableDataListKey($table_data_list_key) {
        $this->_table_data_list_key = $table_data_list_key;
        return $this;
    }

    /**
     * 加入一个数据列表右侧按钮
     * 在使用预置的几种按钮时，比如我想改变编辑按钮的名称
     * 那么只需要$builder->addRightpButton('edit', array('title' => '换个马甲'))
     * 如果想改变地址甚至新增一个属性用上面类似的定义方法
     * 因为添加右侧按钮的时候你并没有办法知道数据ID，于是我们采用__data_id__作为约定的标记
     * __data_id__会在display方法里自动替换成数据的真实ID
     * @param string $type 按钮类型，edit/forbid/recycle/restore/delete/self六种取值
     * @param array  $attribute 按钮属性，一个定了标题/链接/CSS类名等的属性描述数组
     * @return $this
     * @author Doogie <461960962@qq.com>
     */
    public function addRightButton($type, $attribute = null) {
        $set_status_url = 'setstatus';
        $my_attribute = ['class' => ''] ;
        switch ($type) {
            case 'edit':  // 编辑按钮
                // 预定义按钮属性以简化使用
                $my_attribute['text'] = dao_trans('编辑');
                $my_attribute['class'] = 'layui-btn-normal';
                $my_attribute['lay-event']  = 'edit';
                $my_attribute['href']  = url('edit', ['id' => '__data_id__'], '');

                // 如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的顶部按钮
                /**
                * 如果定义了属性数组则与默认的进行合并
                * 用户定义的同名数组元素会覆盖默认的值
                * 比如$builder->addRightButton('edit', array('title' => '换个马甲'))
                */
                if ($attribute && is_array($attribute)) {
                    $my_attribute = array_merge($my_attribute, $attribute);
                }
                break;
            case 'delete':
                // 预定义按钮属性以简化使用
                $my_attribute['text'] = dao_trans('删除');
                $my_attribute['class'] = 'layui-btn-danger';
                $my_attribute['lay-event']  = 'delete';
                $my_attribute['href']  = url($set_status_url, ['status' => 'delete']);

                // 如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的顶部按钮
                if ($attribute && is_array($attribute)) {
                    $my_attribute = array_merge($my_attribute, $attribute);
                }
                break;
            case 'forbid':  // 改变记录状态按钮，会更具数据当前的状态自动选择应该显示启用/禁用
                //预定义按钮属
                $my_attribute['text'] = dao_trans('启用/禁用');
                $my_attribute['class'] = 'layui-btn-warm';
                $my_attribute['lay-event']  = 'forbid';
                $my_attribute['href']  = url($set_status_url, ['status' => 'forbid']);

                // 如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的顶部按钮
                if ($attribute && is_array($attribute)) {
                    $my_attribute = array_merge($my_attribute, $attribute);
                }
                break;
            case 'self':
                // 预定义按钮属性以简化使用
                $my_attribute['lay-event'] = 'self';
                $my_attribute['text'] = dao_trans('自定义按钮');

                // 如果定义了属性数组则与默认的进行合并
                if ($attribute && is_array($attribute)) {
                    $my_attribute = array_merge($my_attribute, $attribute);
                }
                break;
        }
        empty($my_attribute['title']) && $my_attribute['title'] = $my_attribute['text'];
        $my_attribute['class'] .= ' layui-btn layui-btn-xs ';
        // 这个按钮定义好了把它丢进按钮池里
        $node = explode('?', $my_attribute['href'])[0];
        ($this->_auth['super'] || in_array($node, $this->_auth['auth_list'])) && $this->_right_button_list[] = $my_attribute;
        return $this;
    }

    /**
     * 设置分页
     * @param $table_data_page
     * @return $this
     * @author Doogie <461960962@qq.com>
     */
    public function setTableDataPage($table_data_page) {
        $this->_table_data_page = $table_data_page;
        return $this;
    }

    /**
     * 修改列表数据
     * 有时候列表数据需要在最终输出前做一次小的修改
     * 比如管理员列表ID为1的超级管理员右侧编辑按钮不显示删除
     * @param $condition
     * @param $alter_data
     * @return $this
     * @author Doogie <461960962@qq.com>
     */
    public function alterTableData($condition, $alter_data) {
        $this->_alter_data_list[] = array(
            'condition' => $condition,
            'alter_data' => $alter_data
        );
        return $this;
    }

    /**
     * 设置额外功能代码
     * @param string $extra_html 额外功能代码
     * @return $this
     * @author Doogie <461960962@qq.com>
     */
    public function setExtraHtml($extra_html) {
        $this->_extra_html = $extra_html;
        return $this;
    }

    /**
     * 设置页面模版
     * @param string $template 模版
     * @return $this
     * @author Doogie <461960962@qq.com>
     */
    public function setTemplate($template) {
        $this->_template = $template;
        return $this;
    }

    /**
     * 显示页面
     * @param array $assign
     * @param string $view
     * @param string $app
     * @return mixed
     * @author Doogie <461960962@qq.com>
     */
    public function show($assign=[], $view = '', $app = '') {
        //编译top_button_list中的HTML属性
        if ($this->_top_button_list) {
            foreach ($this->_top_button_list as &$button) {
                $button['attribute'] = $this->compileHtmlAttr($button);
            }
        }

        //编译right_button_list中的HTML属性
        if ($this->_right_button_list) {
            foreach ($this->_right_button_list as &$button) {
                $button['attribute'] = $this->compileHtmlAttr($button);
            }
        }

        $assign = array_merge([
            'data_url' => $this->_data_url,
            'meta_title' => $this->_meta_title,          // 页面标题
            'top_button_list' => $this->_top_button_list,     // 顶部工具栏按钮
            'search' => $this->_search,              // 搜索配置
            'tab_nav' => $this->_tab_nav,             // 页面Tab导航
            'table_column_list' => $this->_table_column_list,   // 表格的列
            'table_data_list' => $this->_table_data_list,     // 表格数据
            'table_data_list_key' =>  $this->_table_data_list_key, // 表格数据主键字段名称
            'table_data_page' =>     $this->_table_data_page,    //分页
            'right_button_list' => $this->_right_button_list,   // 表格右侧操作按钮
            'alter_data_list' => $this->_alter_data_list,     // 表格数据列表重新修改的项目
            'extra_html' => $this->_extra_html,    // 额外HTML代码
            'tip' => $this->_tip,
            'css' => $this->_css,
            'js' => $this->_js
        ], $assign);
        unset($this->_meta_title, $this->_top_button_list, $this->_search, $this->_tab_nav, $this->_table_column_list, $this->_alter_data_list,
            $this->_table_data_list, $this->_table_data_list_key,$this->_table_data_page,$this->_right_button_list,$this->_extra_html,$this->_tip);

        return parent::show($assign, $view ?: $this->_template);
    }

    //编译HTML属性
    protected function compileHtmlAttr($attr) {
        $result = array();
        foreach ($attr as $key => $value) {
            $value = htmlspecialchars($value);
            $result[] = "$key=\"$value\"";
        }
        $result = implode(' ', $result);
        return $result;
    }
}
