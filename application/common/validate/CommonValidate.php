<?php

/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/11/8
 * Time: 16:04
 */
namespace app\common\validate;

use think\Validate;
use think\Container;
use think\Db;
use think\exception\ClassNotFoundException;

class CommonValidate extends Validate
{


    /**
     * 验证数据在指定表的字段中是否存在
     * @access protected
     * @param  mixed     $value  字段值
     * @param  mixed     $rule  验证规则 格式：数据表,字段名,主键名
     * @param  array     $data  数据
     * @param  string    $field  验证字段名
     * @return bool
     */
    protected function isExist($value, $rule, $data, $field)
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }

        if (false !== strpos($rule[0], '\\')) {
            // 指定模型类
            $db = new $rule[0];
        } else {
            try {
                $db = Container::get('app')->model($rule[0]);
            } catch (ClassNotFoundException $e) {
                $db = Db::name($rule[0]);
            }
        }

        $key = isset($rule[1]) ? $rule[1] : $field;

        if (strpos($key, '^')) {
            // 支持多个字段验证
            $fields = explode('^', $key);
            foreach ($fields as $key) {
                $map[] = [$key, '=', $data[$key]];
            }
        } else {
            $map[] = [$key, '=', $value];
        }

        $pk = !empty($rule[2]) ? $rule[2] : $db->getPk();

        if ($db->where($map)->field($pk)->find()) {
            return true;
        }

        return false;

    }

    /**
     * 验证数据字段存在则不可为空
     * @access protected
     * @param  mixed     $value  字段值
     * @param  mixed     $rule  验证规则 格式：数据表,字段名,主键名
     * @param  array     $data  数据
     * @param  string    $field  验证字段名
     * @return bool      存在返回TRUE；不存在返回FALSE
     */
    protected function requireExist($value, $rule, $data, $field)
    {
        if(array_key_exists($field,$data)){
            if(trim($value) === ''){
                return false;
            }
        }
        return true;

    }

    /**
     * 验证数据字段是否为空
     * @access protected
     * @param  mixed     $value  字段值
     * @return bool
     *
     * */
    protected function checkEmpty($value)
    {
        return empty($value) ? false : true ;
    }

}