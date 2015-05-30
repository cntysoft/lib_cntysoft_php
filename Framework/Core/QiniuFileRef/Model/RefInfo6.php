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
 * 详细文件引用数据
 */
class RefInfo6 extends BaseModel
{
    private $rid;
    private $filename;
    private $filesize;
    private $uploadDate;
    private $attachment;

    public function getSource()
    {
        return 'sys_file_ref_6';
    }

    public function getRid()
    {
        return $this->rid;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getFilesize()
    {
        return $this->filesize;
    }

    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param int $rid
     * @return \Cntysoft\Framework\Core\QiniuFileRef\Model\RefInfo6Model
     */
    public function setRid($rid)
    {
        $this->rid = $rid;
        return $this;
    }

    /**
     * @param string $filename
     * @return \Cntysoft\Framework\Core\QiniuFileRef\Model\RefInfo6Model
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @param int $filesize
     * @return \Cntysoft\Framework\Core\QiniuFileRef\Model\RefInfo6Model
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;
        return $this;
    }

    /**
     * @param \Datetime $uploadDate
     * @return \Cntysoft\Framework\Core\QiniuFileRef\Model\RefInfo6Model
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;
        return $this;
    }

    /**
     * @param string $attachment
     * @return \Cntysoft\Framework\Core\QiniuFileRef\Model\RefInfo6Model
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
        return $this;
    }

}