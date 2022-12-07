<?php
/**
 * Created by PhpStorm.
 * Script Name: Database.php
 * Create: 2022/10/21 11:14
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\constant;

class Database
{
    const INNODB = 'InnoDB';
    const MYISAM = 'MyISAM';

    /**
     * 字段类型
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function columnTypes()
    {
        return  [
            'int' => 'int',
            'tinyint' => 'tinyint',
            'smallint' => 'smallint',
            'mediumint' => 'mediumint',
            'bigint' => 'bigint',

            'char' => 'char',
            'varchar' => 'varchar',
            //no len
            'tinytext' => 'tinytext',
            'text' => 'text',
            'mediumtext' => 'mediumtext',
            'longtext' => 'longtext',
            'tinyblob' => 'tinyblob',
            'blob' => 'blob',
            'mediumblob' => 'mediumblob',
            'longblob' => 'longblob',
            //no len end

            'date' => 'date', //no len
            'datetime' => 'datetime',
            'time' => 'time',
            'timestamp' => 'timestamp',

            'enum' => 'enum', //no len
            'float' => 'float', //(len, decimal)
            'decimal' => 'decimal', //(len, decimal)
            'double' => 'double', //(len, decimal)

            'binary' => 'binary', //len
            'varbinary' => 'varbinary', //len
            'bit' => 'bit', //len
        ];
    }

    /**
     * 存储引擎
     * @param null $id
     * @return array|mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function engines($id = null){
        $list = [
            self::INNODB => 'InnoDB',
            self::MYISAM => 'MyISAM'
        ];
        return isset($list[$id]) ? $list[$id] : ($id === null ? $list : $list[0]);
    }
}