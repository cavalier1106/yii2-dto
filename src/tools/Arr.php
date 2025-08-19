<?php

namespace liufengsheng\yii\tools;

use yii\helpers\ArrayHelper;

/**
 * 数组公用函数
 * Class Arr
 * @package liufengsheng\yii\tools
 */
class Arr extends ArrayHelper {

    /**
     * 根据字段拷贝
     * @param array $source 数据源
     * @param array $target 拷贝目标数组
     * @param array $keys 要拷贝的key
     * @return mixed
     */
    public function copyByColumn(array $source, array $target, $keys =[]) : array {
        if ( empty($keys) ) {
            return $target;
        }
        foreach ( $keys as $key ) {
            if ( isset($source[$key] )  ){
                $target[$key] = $source[$key];
            }
        }
        return $target;
    }
}