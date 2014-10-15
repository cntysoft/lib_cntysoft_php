<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel\Traits;
use Cntysoft\Kernel;
trait Cacheable
{
    /**
     * @var \Phalcon\Cache\Backend\File $cache
     */
    protected $cache = null;
    
    /**
     * @return \Phalcon\Cache\Backend\File 
     */
    protected function getCache()
    {
        if(null == $this->cache){
            $this->cache = Kernel\make_cache_object();
        }
        return $this->cache;
    }
}