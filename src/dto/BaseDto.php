<?php

namespace liufengsheng\yii\dto;

use ArrayAccess;
use liufengsheng\tools\Arr;
use liufengsheng\tools\Str;
use liufengsheng\tools\Arrayable;
use yii\helpers\ArrayHelper;
use yii\base\Model;

/**
 * Class BaseDTO
 *
 * 数据对象管理
 *
 */
abstract class BaseDto implements ArrayAccess, Arrayable {
    /**
     * 属性数组
     *
     * @var array
     */
    protected $_items = [];

    protected $transformer = BaseTransformer::class;

    # 声明 元素强转类型
    protected $element_type = [];

    /**
     * 默认字段
     * eg. ['title' => 'default']
     *
     * @var array
     */
    protected $include = [];

    /**
     * BaseDTO constructor.
     *
     * @param array $items
     */
    public function __construct($items = []) {
        if (is_array($items)) {
            $this->filter($items);
        } elseif (is_object($items)) {
            if ($items instanceof Model) { //Mysql实体
                /** @var Model $items */
                $this->filter($items->toArray());
            } else { # POPO对象
                $this->filter(get_object_vars($items));
            }
        } elseif ( Str::isJson($items) ) {
            $this->filter(json_decode($items, true));
        } else { # 传进来是NULL 那就走默认值嘛
            $this->filter([]);
        }
    }

    /**
     * 过滤参数
     *
     * @param $items
     */
    protected function filter($items) {
        $this->include = !empty($this->include) ? $this->include : $items;
        $this->_items = [];

        foreach ($this->include as $k => $v) {
            $value = $items[$k] ?? $v;
            $type  = $this->element_type[$k] ?? '';
            $this->_items[$k] = BaseTransformer::transValue($type, $value);
        }
    }

    /**
     * 过滤列表参数
     *
     * @param $list
     */
    protected function filterList($list)
    {
        $this->_items = [];

        foreach ($list as $k => $v) {
            foreach ($this->include as $k2 => $v2) {
                $this->_items[$k][$k2] = isset($list[$k][$k2]) ? $list[$k][$k2] : $this->include[$k2];
            }
        }
    }

    /**
     * 获取属性值
     *
     * @param string $name 名称
     * @return mixed|null
     */
    public function __get($name) {
        if (isset($this->_items[$name])) {
            return $this->_items[$name];
        }
        return null;
    }

    /**
     * 设置属性
     *
     * @param string $name 名称
     * @param string $value 值
     */
    public function __set($name, $value) {
        $type  = $this->element_type[$name] ?? '';
        $value = BaseTransformer::transValue($type, $value);
        Arr::setValue($this->_items, $name, $value);
    }

    /**
     * 是否有属性
     *
     * @param string $name 名称
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->{$name}) || isset($this->_items[$name]);
    }

    /**
     * 转换为JSON输出
     *
     * @return string
     */
    public function toJson(): string {
        return json_encode($this->getAll());
    }

    /**
     * 获取数组
     *
     * @return array
     */
    public function toArray(): array {
        return $this->_toArray($this->_items);
    }

    /**
     * 获取所有属性
     *
     * @return array
     */
    public function getAll(): array {
        return $this->_toArray($this->_items);
    }

    /**
     * 将所有属性转换为数组
     *
     * @param array $arr 需要转换的属性数组
     * @return array
     */
    protected function _toArray($arr): array {
        // 数据类型转换
        $tansformClass = $this->getTransformer();
        if(class_exists($tansformClass)) {
            /** @var BaseTransformer */
            $transformer = new $tansformClass();
            $arr = $transformer->transform($arr);
        }
        return $arr;
    }

    /**
     * 获取字段
     *
     * @return array
     */
    public function getInclude() {
        return $this->include;
    }

    /**
     * 支持按照访问数组方式访问对象属性
     *
     * @param mixed $offset 键
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->_items[$offset];
    }

    /**
     * 删除元素
     *
     * @param mixed $offset 键
     */
    public function offsetUnset($offset) {
        unset($this->_items[$offset]);
    }

    /**
     * 设置元素的值
     *
     * @param mixed $offset 键
     * @param mixed $value 值
     */
    public function offsetSet($offset, $value)
    {
        $this->_items[$offset] = $value;
    }

    /**
     * 判断元素是否存在
     *
     * @param mixed $offset 键
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->_items[$offset]);
    }

    /**
     * @return string
     */
    public function getTransformer(): string
    {
        return $this->transformer;
    }

    /**
     * @param string $transformer
     */
    public function setTransformer(string $transformer)
    {
        $this->transformer = $transformer;
    }
}
