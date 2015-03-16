<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Stdlib;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
use Zend\Stdlib\ArrayUtils as BaseArrayUtils;
/**
 * 跟数组相关的操作函数
 */
abstract class ArrayUtils extends BaseArrayUtils
{
    /**
     * unset指定的数组键值
     * 
     * @param array $data 指定的需要操作的数组
     * @param array $keys 需要删除的数组键值
     * @return array 返回操作结果数组
     */
    public static function unsetKeys(array &$data, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                unset($data[$key]);
            }
        }
    }
    /**
     * 指定的数组键任何一个存在就为真
     * 
     * @param array $data
     * @param array $keys
     * @return boolean
     */
    public static function anyKeyExist(array &$data, array $keys)
    {
        foreach ($keys as $key){
            if(array_key_exists($key, $data)){
                return true;
            }
        }
        return false;
    }
    /**
     * 判断数组书否全部为整型键
     * 
     * @param mixed $value
     * @param boolean $allowEmpty 
     * @return boolean
     */
    public static function isIntegerKeys($value, $allowEmpty = false)
    {
        if (!is_array($value)) {
            return false;
        }
        if (!$value) {
            return $allowEmpty;
        }
        $ret = true;
        foreach ($value as $k => $v) {
            if (!is_int($k)) {
                $ret = false;
            }
        }
        return $ret;
    }

    /**
     * 判断数组是否全部为字符串键
     * 
     * @param mixed $value
     * @param boolean $allowEmpty
     * @return boolean
     */
    public static function isStringKeys($value, $allowEmpty = false)
    {
        if (!is_array($value)) {
            return false;
        }
        if (!$value) {
            return $allowEmpty;
        }
        $ret = true;
        foreach ($value as $k => $v) {
            if (!is_string($k)) {
                $ret = false;
            }
        }
        return $ret;
    }

    /**
     * 将source数组复制到target中，如果已经存在则进行覆盖
     * 
     * @param array $target
     * @param array $source
     * @return array
     */
    public static function apply(array $target, array $source)
    {
        return array_merge($target, $source);
    }

    /**
     * 将source数组复制到target中，如果已经存在不进行覆盖 进行递归操作
     * 
     * @param array $target
     * @param array $source
     * @return array 
     */
    public static function applyIf(array &$target, array $source)
    {
        if (empty($target) && !empty($source)) {
            return $source;
        }
        if (empty($source)) {
            return $target;
        }
        foreach ($source as $key => $value) {
            if (!array_key_exists($key, $target)) {
                $target[$key] = $value;
            } elseif (is_array($value)) {
                $target[$key] = self::apply($target[$key], $source[$key]);
            }
        }
        return $target;
    }

    /**
     * 判断数组里面的值是否全部未数字字符
     * 
     * @param array $data
     * @return boolean
     */
    public static function numeric(array $data)
    {
        if (empty($data)) {
            return false;
        }
        $values = array_values($data);
        $valStr = implode('', $values);
        return ctype_digit($valStr);
    }

    /**
     * Get a single value specified by $path out of $data.
     * Does not support the full dot notation feature set,
     * but is faster for simple read operations.
     *
     * @param array $data Array of data to operate on.
     * @param string|array $path The path being searched for. Either a dot
     * separated string, or an array of path segments.
     * @return mixed The value fetched from the array, or null.
     */
    public static function get(array $data, $path)
    {
        if (empty($data) || empty($path)) {
            return null;
        }
        if (is_string($path)) {
            $parts = explode('.', $path);
        } elseif (is_array($path)) {
            $parts = $path;
        } else {
             Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_ARG_TYPE_ERROR', 'array or string', is_object($path) ? get_class($path) : gettype($iterator)),
            StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
        }
        foreach ($parts as $key) {
            if (is_array($data) && isset($data[$key])) {
                $data = &$data[$key];
            } else {
                return null;
            }
        }
        return $data;
    }
    /**
     * 修改数组特定的项
     * 
     * @param array $target 目标修改数组
     * @param string $path 写入路径
     * @param mixed $data 待写入的数据
     */
    public static function set(array &$target, $path, $data)
    {
        $search =&$target;
        if (is_string($path)) {
            $parts = explode('.', $path);
        } elseif (is_array($path)) {
            $parts = $path;
        } else {
            
           Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_ARG_TYPE_ERROR', 'array or string', is_object($path) ? get_class($path) : gettype($iterator)),
            StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
        }
        $ckey = '';
        foreach ($parts as $key) {
            if('' == $ckey){
                $ckey = $key;
            }else{
                $ckey .= '.'.$key;
            }
            if (is_array($search) && isset($search[$key])) {
                $search = &$search[$key];
            } else{
                //不存在就新建
                if(!isset($search[$key])){
                    $search[$key] = array();
                    $search =  &$search[$key];
                }
            }
        }
        //写入数据
        $search = $data;
    }
    /**
     * 修改指定的路径，如果存在采用的策略是合并
     * 
     * @param array $target 目标修改数组
     * @param string $path 写入路径
     * @param mixed $data 待写入的数据
     * @throws Exception
     */
    public function applyPath(array &$target, $path, $data)
    {
        $search =&$target;
        if (is_string($path)) {
            $parts = explode('.', $path);
        } elseif (is_array($path)) {
            $parts = $path;
        } else {
            Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_ARG_TYPE_ERROR', 'array or string', is_object($path) ? get_class($path) : gettype($path)),
            StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
        }
        $ckey = '';
        foreach ($parts as $key) {
            if('' == $ckey){
                $ckey = $key;
            }else{
                $ckey .= '.'.$key;
            }
            if (is_array($search) && isset($search[$key])) {
                $search = &$search[$key];
            } else{
                //不存在就新建
                if(!isset($search[$key])){
                    $search[$key] = array();
                    $search =  &$search[$key];
                }
            }
        }
        //写入数据
        $search = self::apply($search, $data);
    }
    /**
     * Gets the values from an array matching the $path expression.
     * The path expression is a dot separated expression, that can contain a set
     * of patterns and expressions:
     *
     * - `{n}` Matches any numeric key, or integer.
     * - `{s}` Matches any string key.
     * - `Foo` Matches any key with the exact same value.
     *
     * There are a number of attribute operators:
     *
     *  - `=`, `!=` Equality.
     *  - `>`, `<`, `>=`, `<=` Value comparison.
     *  - `=/.../` Regular expression pattern match.
     *
     * Given a set of User array data, from a `$User->find('all')` call:
     *
     * - `1.User.name` Get the name of the user at index 1.
     * - `{n}.User.name` Get the name of every user in the set of users.
     * - `{n}.User[id]` Get the name of every user with an id key.
     * - `{n}.User[id>=2]` Get the name of every user with an id key greater than or equal to 2.
     * - `{n}.User[username=/^paul/]` Get User elements with username matching `^paul`.
     *
     * @param array $data The data to extract from.
     * @param string $path The path to extract.
     * @return array An array of the extracted values.  Returns an empty array
     *   if there are no matches.
     */
    public static function extract(array $data, $path)
    {
        if (empty($path)) {
            return $data;
        }
        /**
         * 简单路径
         */
        if (!preg_match('/[{\[]/', $path)) {
            return (array) self::get($data, $path);
        }
        if (strpos($path, '[') == false) {
            $tokens = explode('.', $path);
        } else {
            $tokens = String::tokenize($path, '.', '[', ']');
        }
        $key = '__set_item__';
        $context = array($key => array($data));
        foreach ($tokens as $token) {
            $next = array();
            $conditions = false;
            $position = strpos($token, '[');
            if ($position !== false) {
                $conditions = substr($token, $position);
                $token = substr($token, 0, $position);
            }
            foreach ($context[$key] as $item) {
                foreach ($item as $k => $v) {
                    if (self::_matchToken($k, $token)) {
                        $next[] = $v;
                    }
                }
            }
            // Filter for attributes.
            if ($conditions) {
                $filter = array();
                foreach ($next as $item) {
                    if (self::_matchCondition($item, $conditions)) {
                        $filter[] = $item;
                    }
                }
                $next = $filter;
            }
            $context = array($key => $next);
        }
        return $context[$key];
    }

    /**
     * @param string $key
     * @param string $token
     * @return boolean
     */
    private static function _matchToken($key, $token)
    {
        if ('{n}' == $token) {
            return is_numeric($key);
        } elseif ('{s}' == $token) {
            return is_string($key);
        }
        if (is_numeric($token)) {
            return ($key == $token);
        }
        return ($key === $token);
    }

    /**
     * @param array $data
     * @param string $selector
     * @return boolean
     */
    private static function _matchCondition(array $data, $selector)
    {
        preg_match_all(
                '/(\[ (?<attr>[^=><!]+?) (\s* (?<op>[><!]?[=]|[><]) \s* (?<val>[^\]]+) )? \])/x', $selector, $conditions, PREG_SET_ORDER
        );
        foreach ($conditions as $cond) {
            $attr = $cond['attr'];
            $op = isset($cond['op']) ? $cond['op'] : null;
            $val = isset($cond['val']) ? $cond['val'] : null;
            // Presence test.
            if (empty($op) && empty($val) && !isset($data[$attr])) {
                return false;
            }

            // Empty attribute = fail.
            if (!(isset($data[$attr]) || !array_key_exists($attr, $data))) {
                return false;
            }
            $prop = isset($data[$attr]) ? $data[$attr] : null;
            // Pattern matches and other operators.
            if ($op === '=' && $val && $val[0] === '/') {
                if (!preg_match($val, $prop)) {
                    return false;
                }
            } elseif (
                    ($op === '=' && $prop != $val) ||
                    ($op === '!=' && $prop == $val) ||
                    ($op === '>' && $prop <= $val) ||
                    ($op === '<' && $prop >= $val) ||
                    ($op === '>=' && $prop < $val) ||
                    ($op === '<=' && $prop > $val)
            ) {
                return false;
            }
        }
        return true;
    }
}