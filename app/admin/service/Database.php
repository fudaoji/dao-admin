<?php
/**
 * Created by PhpStorm.
 * Script Name: Database.php
 * Create: 2022/12/5 9:02
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\admin\service;

use support\Db;
use think\facade\Db as ThinkDb;

class Database
{
    /**
     * 修改表名
     * @param string $from
     * @param string $to
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function renameTable($from = '', $to = ''){
        self::schema()->rename(self::tableName($from), self::tableName($to));
    }

    /**
     * 获取表名（省略前缀）
     * @param string $name
     * @return string|string[]
     * Author: fudaoji<fdj@kuryun.cn>
     */
    private static function tableName($name = ''){
        $prefix = (string)getenv('DATABASE_PREFIX');
        return str_replace($prefix,'', $name);
    }

    /**
     * 表结构信息
     * @param $table
     * @param null $section
     * @return array|mixed
     * @throws \think\db\exception\BindParamException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getSchema($table, $section = null)
    {
        $database = config('database.connections')[config('database.default')]['database'];
        $schema_raw = $section !== 'table' ? ThinkDb::query("select * from information_schema.COLUMNS where TABLE_SCHEMA = '$database' and table_name = '$table'") : [];
        $columns = [];
        foreach ($schema_raw as $item) {
            $columns[] = self::columnInfoMap($item);
        }
        $table_schema = $section == 'table' || !$section ? ThinkDb::query("SELECT * FROM  information_schema.`TABLES` WHERE  TABLE_SCHEMA='$database' and TABLE_NAME='$table'") : [];
        $indexes = $section == 'keys' || !$section ? ThinkDb::query("SHOW INDEX FROM $table") : [];
        $keys = [];
        foreach ($indexes as $index) {
            $key_name = $index['Key_name'];
            if ($key_name == 'PRIMARY') {
                continue;
            }
            if (!isset($keys[$key_name])) {
                $keys[$key_name] = [
                    'name' => $key_name,
                    'columns' => [],
                    'type' => $index['Non_unique'] == 0 ? 'unique' : 'normal'
                ];
            }
            $keys[$key_name]['columns'][] = $index['Column_name'];
        }

        $data = [
            'table' => !empty($table_schema[0]) ? self::tableInfoMap($table_schema[0]) : [],
            'columns' => $columns,
            'keys' => array_reverse($keys, true)
        ];
        return $section ? $data[$section] : $data;
    }

    /**
     * 获取column
     * @param string $table
     * @param string $column
     * @return array
     * @throws \think\db\exception\BindParamException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getColumn($table = '', $column = ''){
        $database = config('database.connections')[config('database.default')]['database'];
        $list = ThinkDb::query("select * from information_schema.COLUMNS where TABLE_SCHEMA = '$database' and table_name = '$table' and COLUMN_NAME='{$column}'");
        if(count($list)){
            return self::columnInfoMap($list[0]);
        }
        return [];
    }

    /**
     * 验证column是否存在
     * @param string $table
     * @param string $column
     * @return bool
     * @throws \think\db\exception\BindParamException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function checkColumnExist($table = '', $column = ''){
        return count(self::getColumn($table, $column)) ? true : false;
    }

    static function schema()
    {
        return Db::schema();
    }

    /**
     * 获取字段长度
     *
     * @param $schema
     * @return string
     */
    static function getLengthValue($schema)
    {
        $type = $schema['DATA_TYPE'];
        switch ($type){
            case 'int':
            case 'bigint':
            case 'smallint':
            case 'mediumint':
            case 'float':
            case 'decimal':
            case 'double':
                return $schema['NUMERIC_PRECISION'];
            case 'time':
                case 'datetime':
                case 'timestamp':
            return $schema['CHARACTER_MAXIMUM_LENGTH'];
            case 'varchar':
            case 'text':
                case 'char':
            return $schema['CHARACTER_MAXIMUM_LENGTH'];
            case 'enum':
                return implode(',', array_map(function($item){
                    return trim($item, "'");
                }, explode(',', substr($schema['COLUMN_TYPE'], 5, -1))));
        }
        return '';
    }

    /**
     * 表格信息整理
     * @param array $old
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function tableInfoMap($old = []){
        $new = [];
        $map = [
            "TABLE_CATALOG" => "table_catalog",
            "TABLE_SCHEMA" => "table_schema",
            "TABLE_NAME" => "table_name",
            "TABLE_TYPE" => "table_type",
            "ENGINE" => "engine",
            "VERSION" => "version",
            "ROW_FORMAT" => "row_format",
            "TABLE_ROWS" => "table_rows",
            "AVG_ROW_LENGTH" => "avg_row_length",
            "DATA_LENGTH" => "data_length",
            "MAX_DATA_LENGTH" => "max_data_length",
            "INDEX_LENGTH" => "index_length",
            "DATA_FREE" => "data_free",
            "AUTO_INCREMENT" => "auto_increment",
            "CREATE_TIME" => "create_time",
            "UPDATE_TIME" => "update_time",
            "CHECK_TIME" => "check_time",
            "TABLE_COLLATION" => "table_collation",
            "CHECKSUM" => "checksum",
            "CREATE_OPTIONS" => "create_options",
            "TABLE_COMMENT" => "table_comment",
        ];
        foreach ($map as $k => $v){
            isset($old[$k]) && $new[$v] = $old[$k];
        }
        return $new;
    }

    /**
     * 字段信息整理
     * @param array $item
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function columnInfoMap($item = []){
        $field = $item['COLUMN_NAME'];
        $unsigned = 0;
        $zerofill = 0;
        $decimal = 0;
        if(strpos($item['COLUMN_TYPE'], 'unsigned') !== false){
            $unsigned = 1;
        }
        if(strpos($item['COLUMN_TYPE'], 'zerofill') !== false){
            $zerofill = 1;
        }
        if(!empty($item['NUMERIC_SCALE'])){
            $decimal = $item['NUMERIC_SCALE'];
        }
        $type = explode(' ', $item['COLUMN_TYPE'])[0];
        $type = explode('(', $type)[0];

        return  [
            'field' => $field,
            'type' => $type,
            'comment' => $item['COLUMN_COMMENT'],
            'default' => $item['COLUMN_DEFAULT'],
            'length' => self::getLengthValue($item),
            'nullable' => intval($item['IS_NULLABLE'] !== 'NO'),
            'primary_key' => intval($item['COLUMN_KEY'] === 'PRI'),
            'auto_increment' => intval(strpos($item['EXTRA'], 'auto_increment') !== false),
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'decimal' => $decimal
        ];
    }

    public static function dataBaseName()
    {
        return config('thinkorm.connections')[config('thinkorm.default')]['database'];
    }

    public static function dropTable($table_name = '')
    {
        self::schema()->drop(self::tableName($table_name));
    }
}