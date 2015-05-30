<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Core\QiniuFileRef\Model;
use Cntysoft\Phalcon\Mvc\Model as BaseModel;
/**
 * 文件引用主表对象定义
 */
class Entry extends BaseModel
{
    private $rid;
    private $tableId;

    public function getSource()
    {
        return 'sys_file_ref';
    }

    public function getRid()
    {
        return $this->rid;
    }

    public function getTableId()
    {
        return $this->tableId;
    }

    /**
     * @param int $rid
     * @return \Cntysoft\Framework\Core\QiniuFileRef\Model\Entry
     */
    public function setRid($rid)
    {
        $this->rid = $rid;
        return $this;
    }

    /**
     * @param int $tableId
     * @return \Cntysoft\Framework\Core\QiniuFileRef\Model\Entry
     */
    public function setTableId($tableId)
    {
        $this->tableId = $tableId;
        return $this;
    }

}