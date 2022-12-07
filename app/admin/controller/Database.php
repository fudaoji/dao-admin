<?php
/**
 * Created by PhpStorm.
 * Script Name: Database.php
 * Create: 12/3/22 12:01 AM
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\controller;

use app\AdminController;
use app\common\constant\Common;
use think\facade\Db;
use app\common\constant\Database as DatabaseConst;
use Illuminate\Database\Schema\Blueprint;
use app\admin\service\Database as DatabaseService;

class Database extends AdminController
{
    public function __construct(){
        parent::__construct();
    }

    public function tabList($table_name = ''){
        return [
            'basic' => ['title' => '基本信息', 'href' => url('tableedit', ['table_name' => $table_name])],
            'columns' => ['title' => '字段', 'href' => url('columns', ['table_name' => $table_name])],
            'indexs' => ['title' => '索引', 'href' => url('indexs', ['table_name' => $table_name])],
        ];
    }

    public function columnSavePost(){
        if(!$table_name = input('table_name')){
            return $this->error(dao_trans('页面丢失'));
        }
        if(request()->isPost()){
            $post_data = input('post.');

            try{
                //int(10) UNSIGNED ZEROFILL NOT NULL DEFAULT 0 AUTO_INCREMENT
                $type = $post_data['type'];
                $comment = empty($post_data['comment']) ? '' : "COMMENT '{$post_data['comment']}'";
                $nullable = empty($post_data['nullable']) ? 'NOT NULL' : "NULL";
                $default = "DEFAULT '{$post_data['default']}'";
                $unsigned = "";
                $zerofill = "";
                $auto_increment = "";
                //长度
                if (in_array($post_data['type'], ['int', 'tinyint', 'smallint','mediumint','bigint','char','varchar','datetime',
                    'time', 'timestamp', 'binary', 'varbinary', 'bit'])){
                    $type .= "({$post_data['length']})";
                }elseif (in_array($post_data['type'], ['decimal', 'float', 'double'])){
                    $type .= "({$post_data['length']},{$post_data['decimal']})";
                }
                //unsigned
                if (in_array($post_data['type'], ['int', 'tinyint', 'smallint','mediumint','bigint','decimal', 'float', 'double'])){
                    !empty($post_data['unsigned']) && $unsigned .= "UNSIGNED";
                }
                //zerofill
                if (in_array($post_data['type'], ['int', 'tinyint', 'smallint','mediumint','bigint','decimal', 'float', 'double'])){
                    !empty($post_data['zerofill']) && $unsigned .= "ZEROFILL";
                }

                $op = input('op', 'add');
                if($op == 'add'){
                    if(DatabaseService::checkColumnExist($table_name, $post_data['field'])){
                        return $this->error('字段['.$post_data['field'].']已存在');
                    }
                    $sql = "ALTER TABLE `{$table_name}` 
ADD COLUMN `{$post_data['field']}` {$type} {$unsigned} {$zerofill} {$nullable} {$default} {$auto_increment} {$comment} ";
                }else{
                    $sql = "ALTER TABLE `{$table_name}` 
MODIFY COLUMN `{$post_data['field']}` {$type} {$unsigned} {$zerofill} {$nullable} {$default} {$auto_increment} {$comment} ";
                }

                Db::query($sql);
                return $this->success(dao_trans('操作成功'));
            }catch (\Exception $e){
                return $this->error(dao_trans('操作失败'));
            }
        }
    }

    /**
     * 编辑字段
     * @return mixed|\support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\BindParamException
     */
    public function columnEdit(){
        if(!$table_name = input('table_name')){
            return $this->error(dao_trans('页面丢失'));
        }
        if(!$field = input('field')){
            return $this->error(dao_trans('页面丢失'));
        }

        $column = DatabaseService::getColumn($table_name, $field);
        var_dump($column);
        $data = array_merge([
            'length' => 0,
            'decimal' => 0,
            'nullable' => 0,
            'primary_key' => 0,
            'auto_increment' => 0,
            'unsigned' => 0,
            'zerofill' => 0
        ], $column);

        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑字段')  //设置页面标题
            ->setPostUrl(url('columnSavePost', ['table_name' => $table_name,'op' => 'edit'])) //设置表单提交地址
            ->addFormItem('field', 'text', '字段', '长度2-20', [], 'required minlength="2" maxlength="20"')
            ->addFormItem('type', 'chosen', '类型', '类型', DatabaseConst::columnTypes(), 'required')
            ->addFormItem('length', 'number', '长度', '长度', [], 'min=0')
            ->addFormItem('decimal', 'number', '小数点', '小数点', [], 'min=0')
            ->addFormItem('nullable', 'radio', '可为空', '可为空', Common::yesOrNo(), 'required')
            ->addFormItem('primary_key', 'radio', '是否主键', '是否主键', Common::yesOrNo(), 'required')
            ->addFormItem('comment', 'text', '备注', '备注', [], ' maxlength="30"')

            ->addFormItem('default', 'text', '默认值', '默认值', [], 'maxlength=500')
            ->addFormItem('auto_increment', 'radio', '自动递增', '自动递增', Common::yesOrNo())
            ->addFormItem('unsigned', 'radio', '无符号', '无符号', Common::yesOrNo())
            ->addFormItem('zerofill', 'radio', '填充0', '填充0', Common::yesOrNo())
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * ALTER TABLE `fasaas`.`dao_tenant_setting`
     MODIFY COLUMN `update_time` int(10) UNSIGNED ZEROFILL NOT NULL DEFAULT 0 AFTER `create_time`,
    ADD COLUMN `name` varchar(30) NOT NULL DEFAULT '' COMMENT '标识' AFTER `update_time`,
    ADD COLUMN `tenant_id` int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `name`;
     */
    /**
     * 新建字段
     * @return mixed|\support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\BindParamException
     */
    public function columnAdd(){
        if(!$table_name = input('table_name')){
            return $this->error(dao_trans('页面丢失'));
        }
        if(request()->isPost()){
            $post_data = input('post.');

            if (DatabaseService::checkColumnExist($table_name, $post_data['field'])) {
                return $this->error('字段['.$post_data['field'].']已存在');
            }

            try{
                //int(10) UNSIGNED ZEROFILL NOT NULL DEFAULT 0 AUTO_INCREMENT
                $type = $post_data['type'];
                $comment = empty($post_data['comment']) ? '' : "COMMENT '{$post_data['comment']}'";
                $nullable = empty($post_data['nullable']) ? 'NOT NULL' : "NULL";
                $default = "DEFAULT '{$post_data['default']}'";
                $unsigned = "";
                $zerofill = "";
                $auto_increment = "";
                //长度
                if (in_array($post_data['type'], ['int', 'tinyint', 'smallint','mediumint','bigint','char','varchar','datetime',
                    'time', 'timestamp', 'binary', 'varbinary', 'bit'])){
                    $type .= "({$post_data['length']})";
                }elseif (in_array($post_data['type'], ['decimal', 'float', 'double'])){
                    $type .= "({$post_data['length']},{$post_data['decimal']})";
                }
                //unsigned
                if (in_array($post_data['type'], ['int', 'tinyint', 'smallint','mediumint','bigint','decimal', 'float', 'double'])){
                    !empty($post_data['unsigned']) && $unsigned .= "UNSIGNED";
                }
                //zerofill
                if (in_array($post_data['type'], ['int', 'tinyint', 'smallint','mediumint','bigint','decimal', 'float', 'double'])){
                    !empty($post_data['zerofill']) && $unsigned .= "ZEROFILL";
                }

                $sql = "ALTER TABLE `{$table_name}` 
ADD COLUMN `{$post_data['field']}` {$type} {$unsigned} {$zerofill} {$nullable} {$default} {$auto_increment} {$comment} ";

                Db::query($sql);
                return $this->success(dao_trans('新增成功'));
            }catch (\Exception $e){
                return $this->error(dao_trans('新增失败' . $e->getMessage()));
            }
        }

        $data = [
            'length' => 0,
            'decimal' => 0,
            'nullable' => 0,
            'primary_key' => 0,
            'auto_increment' => 0,
            'unsigned' => 0,
            'zerofill' => 0
        ];
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增字段')  //设置页面标题
            ->setPostUrl(url('columnSavePost', ['table_name' => $table_name])) //设置表单提交地址
            ->addFormItem('field', 'text', '字段', '长度2-20', [], 'required minlength="2" maxlength="20"')
            ->addFormItem('type', 'chosen', '类型', '类型', DatabaseConst::columnTypes(), 'required')
            ->addFormItem('length', 'number', '长度', '长度', [], 'min=0')
            ->addFormItem('decimal', 'number', '小数点', '小数点', [], 'min=0')
            ->addFormItem('nullable', 'radio', '可为空', '可为空', Common::yesOrNo(), 'required')
            ->addFormItem('primary_key', 'radio', '是否主键', '是否主键', Common::yesOrNo(), 'required')
            ->addFormItem('comment', 'text', '备注', '备注', [], ' maxlength="30"')

            ->addFormItem('default', 'text', '默认值', '默认值', [], 'maxlength=500')
            ->addFormItem('auto_increment', 'radio', '自动递增', '自动递增', Common::yesOrNo())
            ->addFormItem('unsigned', 'radio', '无符号', '无符号', Common::yesOrNo())
            ->addFormItem('zerofill', 'radio', '填充0', '填充0', Common::yesOrNo())
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 字段
     * @return mixed|\support\Response
     * @throws \think\db\exception\BindParamException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function columns(){
        if(!$table_name = input('table_name')){
            return $this->error(dao_trans('页面丢失'));
        }

        if(request()->isPost()){
            $post_data = input('post.');
            $list = DatabaseService::getSchema($table_name, 'columns');
            $total = count($list);
            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setTabNav($this->tabList($table_name), 'columns')
            ->addTopButton('addnew', ['href' => url('columnAdd', ['table_name' => $table_name])])
            ->addTableColumn(['title' => '序号', 'type' => 'index','minwidth' => 70])
            ->addTableColumn(['title' => '字段', 'field' => 'field'])
            ->addTableColumn(['title' => '备注', 'field' => 'comment'])
            ->addTableColumn(['title' => '类型', 'field' => 'type'])
            ->addTableColumn(['title' => '长度', 'field' => 'length'])
            ->addTableColumn(['title' => '默认值', 'field' => 'default'])
            ->addTableColumn(['title' => '主键', 'field' => 'primary_key', 'type' => 'enum','options' => Common::yesOrNo()])
            ->addTableColumn(['title' => '自增', 'field' => 'auto_increment', 'type' => 'enum','options' => Common::yesOrNo()])
            ->addTableColumn(['title' => '可为空', 'field' => 'nullable', 'type' => 'enum','options' => Common::yesOrNo()])
            ->addTableColumn(['title' => '操作', 'width' => 120, 'type' => 'toolbar'])
            ->addRightButton('edit', ['href' => url('columnedit', ['table_name' => $table_name, 'field' => '__data_field__'])])
            ->addRightButton('delete', ['href' => url('columndelpost', ['table_name' => $table_name, 'field' => '__data_field__'])]);
        return $builder->show();
    }

    /**
     * 数据表
     * @return mixed|\support\Response
     * @throws \think\db\exception\BindParamException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function tables(){
        if(request()->isPost()){
            $post_data = input('post.');
            $where = 'and 1=1 ';
            !empty($post_data['search_key']) && $where .= ' and table_name like "%'.$post_data['search_key'].'%"';
            $field = input('field', 'table_name');
            $order = input('asc');
            $database = DatabaseService::dataBaseName();

            $limit = ($post_data['page']-1) * $post_data['limit'] .','. $post_data['limit'];
            $total = Db::query("SELECT count(*) as dao_count FROM  information_schema.`TABLES` WHERE TABLE_SCHEMA='$database' {$where} limit 1")[0]['dao_count'];
            if ($total) {
                $list = Db::query("SELECT table_name ,table_rows, table_comment, engine,table_collation,create_time,update_time FROM  information_schema.`TABLES` WHERE  TABLE_SCHEMA='$database' {$where} order by {$field} {$order} limit {$limit}");
            } else {
                $list = [];
            }

            return $this->success('success', '', ['total' => $total, 'list' => $list]);
        }

        $builder = new ListBuilder();
        $builder->setSearch([
            ['type' => 'text', 'name' => 'search_key', 'title' => '搜索词','placeholder' => '表名'],
        ])
            ->addTopButton('addnew', ['href' => url('tableAdd')])
            ->addTableColumn(['title' => '序号', 'type' => 'index','minwidth' => 70])
            ->addTableColumn(['title' => '表名', 'field' => 'table_name'])
            ->addTableColumn(['title' => '记录数', 'field' => 'table_rows'])
            ->addTableColumn(['title' => '备注', 'field' => 'table_comment'])
            ->addTableColumn(['title' => '引擎', 'field' => 'engine'])
            ->addTableColumn(['title' => '字符集', 'field' => 'table_collation'])
            ->addTableColumn(['title' => '创建时间', 'field' => 'create_time'])
            ->addTableColumn(['title' => '操作', 'width' => 120, 'type' => 'toolbar'])
            ->addRightButton('edit', ['href' => url('tableedit', ['table_name' => '__data_table_name__'])])
            ->addRightButton('delete', ['href' => url('tabledelpost', ['table_name' => '__data_table_name__'])]);
        return $builder->show();
    }

    /**
     * 新建表
     * @return mixed|\support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     * @throws \think\db\exception\BindParamException
     */
    public function tableAdd(){
        if(request()->isPost()){
            $post_data = input('post.');

            if (Db::query("SHOW TABLES LIKE '{$post_data['table_name']}'")) {
                return $this->error($post_data['table_name'] . '表已存在');
            }

            try{
                /* tp的collect与illuminate的collect冲突
                 * DatabaseService::schema()->create($post_data['table_name'], function (Blueprint $table) use ($post_data) {
                    //$table->comment = $post_data['table_comment'];
                    $table->engine = $post_data['engine'];
                    $table->charset = $post_data['charset'];
                    $table->collation = $post_data['collation'];
                    $table->increments('id');
                    $table->integer('create_time', false, true);
                    $table->integer('update_time', false, true);
                });*/
                $sql = "CREATE TABLE `__NAME__`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE = __ENGINE__  COMMENT = '__COMMENT__';";
                $sql = str_replace(
                    ['__NAME__', '__COMMENT__','__ENGINE__'],
                    [$post_data['table_name'],$post_data['table_comment'],$post_data['engine']], $sql
                );
                Db::query($sql);
                return $this->success(dao_trans('创建成功'));
            }catch (\Exception $e){
                return $this->error('创建表失败：' . $e->getMessage());
            }
        }

        $data = [
            'engine' => DatabaseConst::INNODB,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci'
        ];
        $builder = new FormBuilder();
        $builder->setMetaTitle('新增表')  //设置页面标题
            ->setPostUrl(url(__FUNCTION__)) //设置表单提交地址
            ->addFormItem('table_name', 'text', '表名', '长度不超过20', [], 'required minlength="2" maxlength="20"')
            ->addFormItem('table_comment', 'text', '表注释', '表注释', [], 'required minlength="1" maxlength="30"')
            ->addFormItem('engine', 'radio', 'Engine', '存储引擎', DatabaseConst::engines(), 'required')
            ->setFormData($data);

        return $builder->show();
    }

    public function tableEdit(){
        if(request()->isPost()){
            $post_data = input('post.');
            if($post_data['old_table_name'] != $post_data['table_name']){
                DatabaseService::renameTable($post_data['old_table_name'], $post_data['table_name']);
            }
            try{
                $sql = "ALTER TABLE `{$post_data['table_name']}` ENGINE = {$post_data['engine']}, COMMENT = '{$post_data['table_comment']}'";
                Db::query($sql);
                return $this->success(dao_trans('编辑成功'));
            }catch (\Exception $e){
                return $this->error('编辑表失败：' . $e->getMessage());
            }
        }

        $params = input();
        $data = DatabaseService::getSchema($params['table_name'], 'table');
        $data['old_table_name'] = $data['table_name'];
        $builder = new FormBuilder();
        $builder->setMetaTitle('编辑表')  //设置页面标题
            ->setTabNav($this->tabList($params['table_name']), 'basic')
            ->setPostUrl(url(__FUNCTION__)) //设置表单提交地址
            ->addFormItem('old_table_name', 'hidden', '表名', '长度不超过20', [], 'required minlength="2" maxlength="20"')
            ->addFormItem('table_name', 'text', '表名', '长度不超过20', [], 'required minlength="2" maxlength="20"')
            ->addFormItem('table_comment', 'text', '表注释', '表注释', [], 'required minlength="1" maxlength="30"')
            ->addFormItem('engine', 'radio', 'Engine', '存储引擎', DatabaseConst::engines(), 'required')
            ->setFormData($data);

        return $builder->show();
    }

    /**
     * 删除表
     * @return \support\Response
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function tableDelPost(){
        $data = input();
        DatabaseService::dropTable($data['table_name']);
        return $this->success(dao_trans('删除成功！'));
    }
}