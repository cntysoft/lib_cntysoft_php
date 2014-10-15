<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Core\Domain\Model;
use Cntysoft\Phalcon\Mvc\Model as BaseModel;
/**
 * 域名映射表
 */
class Map extends BaseModel
{
    private $domain;
    private $churchId;
    public function getSource()
    {
        return 'sys_url_bind_map';
    }
    
    public function getDomain()
    {
        return $this->domain;
    }

    public function getChurchId()
    {
        return (int)$this->churchId;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function setChurchId($christId)
    {
        $this->churchId = (int)$christId;
        return $this;
    }
}