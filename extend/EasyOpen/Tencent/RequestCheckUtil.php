<?php
/**
 * Created by PhpStorm.
 * Script Name: RequestCheckUtil.php
 * Create: 2023/2/4 17:57
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent;

class RequestCheckUtil
{
    /**
     * 校验字段filedName的值$value非空
     * @param $value
     * @param $fieldName
     * @return bool|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkNotNull($value, $fieldName) {
        if(self::checkEmpty($value)) {
            return "缺少必要参数: " . $fieldName;
        }

        return true;
    }

    /**
     * 校验filedName值value的长度
     * @param $value
     * @param $maxLength
     * @param $fieldName
     * @return bool|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkMaxLength($value, $maxLength, $fieldName) {
        if(!self::checkEmpty($value) && mb_strlen($value, "UTF-8") > $maxLength) {
            return "参数: " .$fieldName . " 的长度应小于 " . $maxLength;
        }
        return true;
    }

    /**
     * 校验fieldName的值value的最大列表长度
     * @param $value
     * @param $maxSize
     * @param $fieldName
     * @return bool|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkMaxListSize($value, $maxSize, $fieldName) {
        if (self::checkEmpty($value)) return "$fieldName 不能为空";
        if(strpos($value, ',') !== false){
            $list = explode(',', $value);
        }else{
            $list = (array) $value;
        }
        if(count($list) > $maxSize) {
            return "参数 ". $fieldName . " 的值的最大列表长度不能大于 " . $maxSize;
        }

        return true;
    }

    /**
     * 校验字段fieldName的值value的最大值
     * @param $value
     * @param $maxValue
     * @param $fieldName
     * @return bool|string
     * @throws \Exception
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkMaxValue($value, $maxValue, $fieldName) {
        if (self::checkEmpty($value)) return false;
        self::checkNumberic($value, $fieldName);

        if ($value > $maxValue) {
            return "参数 " . $fieldName . " 应小于 " . $maxValue;
        }
        return true;
    }

    /**
     * 校验字段fieldName的值value的最小值
     * @param $value
     * @param $minValue
     * @param $fieldName
     * @return bool|string
     * @throws \Exception
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkMinValue($value, $minValue, $fieldName) {
        if (self::checkEmpty($value)) return false;
        self::checkNumberic($value, $fieldName);

        if ($value < $minValue) {
            return " 应大于 " . $minValue;
        }

        return true;
    }

    /**
     * 校验字段filedName的值value是否是number
     * @param $value
     * @param $fieldName
     * @return bool|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkNumberic($value, $fieldName) {
        if (!is_numeric($value)) {
            return "参数 " . $fieldName . " 的值不是数字 : " . $value;
        }

        return true;
    }

    /**
     * 校验字段filedName的值value是否是array
     * @param $value
     * @param $fieldName
     * @return bool|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkArray($value, $fieldName) {
        if(! is_array($value)) {
            return "参数 " . $fieldName . " 的值不是数组 : " . $value;
        }

        return true;
    }

    /**
     * 校验$value是否非空
     * @param string $value
     * @return boolean
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (is_string($value) && trim($value) === "")
            return true;

        return false;
    }

    /**
     * 校验fieldName的值是否是合法范围内的值
     * @param string $value
     * @param array $range
     * @return boolean
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkIn($value, $range = []) {
        if(self::checkEmpty($value)) return false;
        if(in_array($value, $range)) {
            return true;
        }
        return false;
    }

    /**
     * 校验时间格式
     * @param string $date
     * @param string $fieldName
     * @return bool|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function checkDate($date = '', $fieldName = ''){
        $timestamp = strtotime($date);
        if($timestamp !== strtotime(date('YmdHis', $timestamp))){
            return "参数 " . $fieldName . " 的值合法的时间格式 : " . $date;
        }
        return  true;
    }
}