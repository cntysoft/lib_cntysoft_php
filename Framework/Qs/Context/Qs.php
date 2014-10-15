<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
use Cntysoft\Framework\Qs\Engine\Tag\Label;
use Cntysoft\Framework\Qs\Engine\Tag\PhpVar;
use Cntysoft\Framework\Qs\Engine\Tag\Ds;
use Cntysoft\Framework\Qs\Engine\Tag\DsField;
use Cntysoft\Framework\Qs\Engine\Tag\FuncInvoker;
use Cntysoft\Framework\Qs\View;
/**
 * 在这里开启生成是否的内存缓存
 */
final class Qs
{
    /**
     * @var \Cntysoft\Framework\Qs\Engine\Tag\Label $label
     */
    protected static $label = null;
    /**
     * @var \Cntysoft\Framework\Qs\Engine\Tag\PhpVar $phpVar
     */
    protected static $phpVar = null;
    /**
     * @var \Cntysoft\Framework\Qs\Engine\Tag\Ds  $ds
     */
    protected static $ds = null;
    /**
     * @var \Cntysoft\Framework\Qs\Engine\Tag\DsField  $dsField
     */
    protected static $dsField = null;
    /**
     * @var \Cntysoft\Framework\Qs\Engine\Tag\FuncInvoker  $funcInvoker
     */
    protected static $funcInvoker = null;
    /**
     * @var \Cntysoft\Framework\Qs\Engine\Php $engine
     */
    protected static $engine = null;
    /**
     * 生成时候使用的内存缓存
     * 
     * @var array $cache
     */
    protected static $cache = array();

    /**
     * 解析普通label标签, 标签支持两种方式正常解析与计算求值
     * 
     * @param string $id 标签指定的id
     * @param array $params
     * @return null | mixed
     */
    public static function Label($id, array $params = array(), $eval = false)
    {
        $key = md5(View::TAG_LABEL.serialize(func_get_args()));
        if (self::$engine->getView()->getMode() == View::ENGINE_MODE_BUILD) {
            if (isset(self::$cache[$key])) {
                if ($eval) {
                    return self::$cache[$key];
                } else {
                    echo self::$cache[$key];
                }
            }else{
                if($eval){
                    self::$cache[$key] = self::doLableRender($id, $params, $eval);
                    return self::$cache[$key];
                }else{
                    ob_start();
                    self::doLableRender($id, $params, $eval);
                    $data = ob_get_clean();
                    self::$cache[$key] = $data;
                    echo $data;
                }
            }
        } else {
            if($eval){
                return self::doLableRender($id, $params, $eval);
            }
            self::doLableRender($id, $params, $eval);
        }
    }

    protected static function doLableRender($id, array $params = array(), $eval = false)
    {
        if (null == self::$label) {
            self::$label = new Label(self::$engine);
        }
        if ($eval) {
            ob_start();
            self::$label->render($id, $params);
            return ob_get_clean();
        }
        self::$label->render($id, $params);
    }

    /**
     * 获取制定KEY获取模板变量
     * 
     * @param string $key
     */
    public static function PhpVar($key)
    {
        if (null == self::$phpVar) {
            self::$phpVar = new PhpVar(self::$engine);
        }
        self::$phpVar->getVar($key);
    }

    /**
     * @param string $id 数据源ID
     * @param string $group 数据源分组
     * @param array $params
     */
    public static function Ds($id, $group, array $params = array())
    {
        if (null == self::$ds) {
            self::$ds = new Ds(self::$engine);
        }
        self::$ds->register($id, $group, $params);
    }

    /**
     * 
     * @param string $group 与之关联的数据源
     * @param string $key 需要获取的数据字段名称
     * @param boolean $eval 是否返回数据
     */
    public static function DsField($group, $key, $eval = false)
    {
        if (null == self::$dsField) {
            self::$dsField = new DsField(self::$engine);
        }
        if ($eval) {
            return self::$dsField->getFieldValue($group, $key, $eval);
        }
        self::$dsField->getFieldValue($group, $key);
    }

    /**
     * 系统内置函数集合
     * 
     * @param string $name 函数名称
     */
    public static function Sys($name)
    {
        if (null == self::$funcInvoker) {
            self::$funcInvoker = new FuncInvoker(self::$engine);
        }
        $args = func_get_args();
        array_shift($args);
        self::$funcInvoker->call(View::TAG_SYS, $name, $args);
    }

    /**
     * 站点配置函数集合
     * 
     * @param string $name 函数名称
     */
    public static function SiteConfig($name)
    {
        if (null == self::$funcInvoker) {
            self::$funcInvoker = new FuncInvoker(self::$engine);
        }
        $args = func_get_args();
        array_shift($args);
        self::$funcInvoker->call(View::TAG_SITE_CONFIG, $name);
    }

    /**
     * @param \Cntysoft\Framework\Qs\Engine\EngineInterface $engine
     */
    public static function setEngine($engine)
    {
        self::$engine = $engine;
    }

}