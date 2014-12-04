<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Qs\Lib;
/**
 * 站点配置类定义
 */
class SiteConfig
{
    /**
     * 获取系统皮肤地址
     *
     * 调用方式
     * <code>Qs::SiteConfig('skinPath');</code>
     * @return string
     */
    public static function skinPath()
    {
        return self::uiPath().'/Skins';
    }

    /**
     * 获取系统Ui路经
     *
     * 调用方式
     * <code>Qs::SiteConfig('uiPath');</code>
     * @return string
     */
    public static function uiPath()
    {
        return '/Ui';
    }

    /**
     * 获取前端Js路径
     * 
     * 调用方式
     * <code>Qs::SiteConfig('jsPath');</code>
     * @return string
     */
    public static function jsPath()
    {
        return self::uiPath().'/JsLib';
    }

    /**
     * jquery框架地址
     * 
     * 调用方式
     * <code>Qs::SiteConfig('jqueryPath');</code>
     * @return string
     */
    public static function jqueryPath()
    {
        return self::jsPath().'/Jquery';
    }

    /**
     * bootstrap框架相关的路径
     * 
     * 
     * 调用方式
     * <code>Qs::SiteConfig('bootstrapPath');</code>
     * @return string
     */
    public static function bootstrapPath()
    {
       return self::jsPath() . '/Bootstrap';
    }

    /**
     * 获取系统ExtJs路经
     *
     * 调用方式
     * <code>Qs::SiteConfig('extJsPath');</code>
     * return string
     */
    public static function extJsPath()
    {
        return self::sysJsLibraryPath().'/ExtJs';
    }
    
    /**
     * 获取系统
     */
    public static function sysJsLibraryPath()
    {
        return '/JsLibrary';
    }
}