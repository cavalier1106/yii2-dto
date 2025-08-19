<?php

namespace liufengsheng\yii\dto;


/**
 * 类似转换器，值传换器
 * Class BaseTransformer
 * @package liufengsheng\yii\dto
 */
class BaseTransformer {
    public const INT      = 'int';
    public const BOOLEAN  = 'boolean';
    public const FLOAT    = 'float';
    public const STRING   = 'string';
    public const DOUBLE   = 'double';
    public const JSON_STR = 'json_str'; # json_encode后的字符串，经过处理会反解为相应的array
    public const JSON     = 'json';     # json_encode后的字符串，经过处理会反解为相应的array
    public const PRICE    = 'price';    # 价钱 两位小数
    public const OBJECT   = 'object';   # json_decode 直接返回的object


    /**
     * 转换数值
     * @param $type
     * @param $value
     * @return array|bool|float|int|mixed|string
     */
    public static function transValue($type, $value) {
        switch ( $type ) {
            case self::INT :
                return (int)$value;
            case self::STRING :
                return (string)$value;
            case self::JSON :
            case self::JSON_STR :
                if ( empty($value) ) {
                    return [];
                } else {
                    if ( is_string($value) ) {
                        return json_decode($value, true);
                    }
                }
            case self::DOUBLE :
                return (double)$value;
            case self::FLOAT :
                return (float)$value;
            case self::OBJECT :
                if ( empty($value) ) {
                    return [];
                } else {
                    if ( is_string($value) ) {
                        return json_decode($value );
                    }
                }
            case self::BOOLEAN :
                return (bool)$value;
            case self::PRICE :
                return sprintf("%.2f", $value);
        }
        
        # 如果是特殊类，为了风险控制暂时只支持 BaseDto 及 BaseDtoList
        if ( class_exists($type) ) {
            if ( $value instanceof $type ) {
                return $value; # 如果是已经转了的 就直接返回
            }
            $obj = new \ReflectionClass($type);
            # 是否Dto子类
            if ( $obj->isSubclassOf(BaseDto::class) ) {
                return $obj->newInstance($value);
            }
        }
        return $value;
    }

    /**
     * 过滤类型
     * @return array
     */
    public function filter($arr = null) : array
    {
        $allItems = [];
        if (!empty($arr)) {
            foreach ($arr as $key => $value) {
                if ($value instanceof BaseDto) {
                    $allItems[$key] = $value->getAll();
                } elseif (is_array($value)) {
                    $allItems[$key] = $this->filter($value);
                } else {
                    $allItems[$key] = $value;
                }
            }
        }

        return $allItems;
    }

    /**
     * 将指定的array的指定字段转成指定的类似
     *
     * @param BaseDto $arr
     * @return array
     */
    public function transform(array $arr) : array
    {
        if (!empty($arr)) {
            $arr = $this->filter($arr);
        }
        return $arr;
    }
}
