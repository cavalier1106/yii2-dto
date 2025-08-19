<?php

namespace liufengsheng\yii\tools;

use yii\helpers\StringHelper;

/**
 * Class Str
 *
 * 字符串公用操作函数
 *
 * @uses    Common operations for string.
 *
 * @author  Speciallan
 * @version 1.0
 * @package liufengsheng\yii\tools
 */
class Str extends StringHelper {

    /**
     * 判断字符串是否是json
     *
     * @param string $str 字符串
     * @return boolean
     */
    public static function isJson($string)
    {
        return ((is_string($string) && (is_object(json_decode($string))
                || is_array(json_decode($string))))) ? true : false;
    }

}
