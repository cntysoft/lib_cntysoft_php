<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Core\Domain;
use Cntysoft\Kernel;
/**
 * 处理系统域名绑定映射相关信息
 */
class Binder
{
    const MAP_CACHE_KEY = 'MAP_CACHE_KEY';
    
    /**
     * @var \Phalcon\Cache\Backend\File $cache
     */
    protected static $cache = null;
    /**
     * 暂时用文件缓存，到时候使用内存数据库
     * 
     * @return \Phalcon\Cache\Backend\File
     */
    protected static function getCache()
    {
        if(null == self::$cache){
            self::$cache = Kernel\make_cache_object();
        }
        return self::$cache;
    }
    
    /**
     * 转换域名为教堂的id
     * 
     * @param string $domain
     */
    public static function transform($domain)
    {
        $cache = self::getCache();
        $map = $cache->get(self::MAP_CACHE_KEY);
        if(null == $map){
            $map = array();
            $set = Model\Map::find();
            foreach ($set as $key => $value){
                $map[$value->getDomain()] = $value->getChurchId();
            }
            $cache->save(self::MAP_CACHE_KEY, $map);
        }
        if(isset($map[$domain])){
            return $map[$domain];
        }
        return -1;
    }
}