<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Core\FileRef;
use Cntysoft\Kernel;
use Cntysoft\Framework\Core\ErrorType as CoreErrorType;
use Cntysoft\Stdlib\Filesystem;
use Cntysoft\Kernel\StdDir;
use Cntysoft\Framework\Core\FileRef\Model\Entry as EntryModel;
use Cntysoft\Framework\Core\FileRef\Model\Unused as UnusedModel;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\Manager as EventsManager;
/**
 * 文件引用管理器, 这个仅仅提供一套文件引用的机制
 */
class Manager implements EventsAwareInterface
{
    /**
     * 临时文件引用的表ID
     */
    CONST UNUSED_TABLE_ID = 127;
    /**
     * 表数量的掩码
     */
    CONST TABLE_MASK = 10;
    /**
     * 文件引用详细信息对象模型
     */
    CONST REF_INFO_M_CLS = 'Cntysoft\Framework\Core\FileRef\Model\RefInfo%d';
    /**
     * 未确定的引用
     */
    CONST UNUSED_M_CLS = 'Cntysoft\Framework\Core\FileRef\Model\Unused';
    /**
     * 文件引用类名称
     */
    CONST ENTRY_M_CLS = 'Cntysoft\Framework\Core\FileRef\Model\Entry';

    /**
     * 每次执行操作的时候指定的数据表的ID
     * 
     * @var int $currentTableId
     */
    protected $currentTableId;
    /**
     * @var \Phalcon\Events\Manager  $eventsManager
     */
    protected $eventsManager;

    /**
     * 添加一个附件引用到文件引用管理器, 用这个接口添加的引用默认的详细模型是UnusedModel
     * 
     * $refInfo的格式如下
     * <code>
     * array(
     *      'filename' => '文件名称',
     *      'filesize' => '文件大小',
     *      'attachment' => '真实的文件地址'
     * );
     * </code>
     * @param int $churchId
     * @param array $refInfo 
     * @return int 临时的文件引用ID
     */
    public function addTempFileRef(array $refInfo)
    {
        $this->currentTableId = self::UNUSED_TABLE_ID;
        $db = Kernel\get_db_adapter();
        try {
            $db->begin();
            $entry = new EntryModel();
            $entry->setTableId(self::UNUSED_TABLE_ID);
            $entry->create();
            $rid = $entry->getRid();
            $refInfo['rid'] = $rid;
            $refInfo['uploadDate'] = time();
            $refInfoCls = self::getRefInfoModelCls(self::UNUSED_TABLE_ID);
            $detail = new $refInfoCls();
            $detail->create($refInfo);
            $db->commit();
            $events = $this->getEventsManager();
            $events->fire('filerefmanager:addTempFileRef', $this, array($entry, $detail));
            return $rid;
        } catch (\Exception $ex) {
            $errorType = CoreErrorType::getInstance();
            $db->rollback();
            Kernel\throw_exception($ex, $errorType);
        }
    }

    /**
     * 提交一个为确定的文件引用
     * 
     * @param int $rid
     * @return int 确定的分表的id
     * @throws \Cntysoft\Framework\Core\FileRef\Exception
     */
    public function confirmFileRef($rid)
    {
        $entry = EntryModel::findFirst($rid);
        if (!$entry) {
            $errorType = CoreErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                    $errorType->msg('E_FILE_REF_ENTRY_NOT_EXIST', $rid), $errorType->code('E_FILE_REF_ENTRY_NOT_EXIST')), $errorType);
        }
        $tableId = (int) $entry->getTableId();
        if ($tableId !== self::UNUSED_TABLE_ID) {
            //已经正常了
            return $tableId;
        }
        $tableId = $rid % self::TABLE_MASK;
        $db = Kernel\get_db_adapter();
        try {
            $db->begin();
            $entry->setTableId($tableId);
            $entry->update();
            $unused = UnusedModel::findFirst($rid);
            $refValues = $unused->toArray();
            $unused->delete();
            $refInfoCls = self::getRefInfoModelCls($tableId);
            $detail = new $refInfoCls();
            $detail->create($refValues);
            $db->commit();
            $events = $this->getEventsManager();
            $events->fire('filerefmanager:confirmFileRef', $this, array($entry, $detail));
        } catch (\Exception $ex) {
            $errorType = CoreErrorType::getInstance();
            $db->rollback();
            Kernel\throw_exception($ex, $errorType);
        }
    }

    /**
     * 删除指定的文件引用
     * 
     * @author Changwang <chenyongwang1104@163.com>
     * @param int $rid
     * @throws \Cntysoft\Framework\Core\FileRef\Exception
     */
    public function removeFileRef($rid)
    {
        $entry = EntryModel::findFirst($rid);
        if (!$entry) {
            $errorType = CoreErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                    $errorType->msg('E_FILE_REF_ENTRY_NOT_EXIST', $rid), $errorType->code('E_FILE_REF_ENTRY_NOT_EXIST')), $errorType);
        }
        $tableId = (int) $entry->getTableId();
        $refInfoCls = $this->getRefInfoModelCls($tableId);
        $refInfo = $refInfoCls::findFirst($rid);
        $attachment = $refInfo->getAttachment();
        $filename = CNTY_ROOT_DIR.DS.$attachment;
        $db = Kernel\get_db_adapter();
        try {
            $db->begin();
            if (file_exists($filename)) {
                //在这里文件要是不存在无所谓
                Filesystem::deleteFile($filename);
            }
            $entry->delete();
            $refInfo->delete();
            $db->commit();
            $events = $this->getEventsManager();
            $events->fire('filerefmanager:confirmFileRef', $this, array($entry, $refInfo));
        } catch (\Exception $ex) {
            $errorType = CoreErrorType::getInstance();
            $db->rollback();
            Kernel\throw_exception($ex, $errorType);
        }
    }
    
    /**
     * 获取附件链接
     * 
     * @param integer $rid
     * @return string
     */
    public function getAttachment($rid)
    {
        $entry = EntryModel::findFirst($rid);
        if (!$entry) {
            $errorType = CoreErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                    $errorType->msg('E_FILE_REF_ENTRY_NOT_EXIST', $rid), $errorType->code('E_FILE_REF_ENTRY_NOT_EXIST')), $errorType);
        }
        $tableId = (int) $entry->getTableId();
        $refInfoCls = self::getRefInfoModelCls($tableId);
        $refInfo = $refInfoCls::findFirst($rid);
        if(!$refInfo){
            $errorType = CoreErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                    $errorType->msg('E_FILE_REF_ENTRY_NOT_EXIST', $rid), $errorType->code('E_FILE_REF_ENTRY_NOT_EXIST')), $errorType); 
        }
        return $refInfo->getAttachment();
    }
    
    /**
     * 设置事件管理器
     * 
     * @param \Phalcon\Events\Manager $eventsManager
     * @return \Cntysoft\Framework\Core\FileRef\Manager
     */
    public function setEventsManager($eventsManager)
    {
        $this->eventsManager = $eventsManager;
        return $this->eventsManager;
    }

    /**
     * @return \Phalcon\Events\Manager $eventsManager
     */
    public function getEventsManager()
    {
        if (null == $this->eventsManager) {
            $this->eventsManager = new EventsManager();
        }
        return $this->eventsManager;
    }
    
    
    
    /**
     * 清理不用的文件引用
     *
     * @param int $limit 每次运行的个数
     */
    public function clearUnusedFileRefs($limit = 20)
    {
        $refs = EntryModel::find(array(
                   'tableId = ?0',
                   'bind'  => array(
                      0 => self::UNUSED_TABLE_ID
                   ),
                   'limit' => $limit
        ));

        if (count($refs) > 0) {
            $db = Kernel\get_db_adapter();
            try {
                $db->begin();
                $events = $this->getEventsManager();

                foreach ($refs as $ref) {
                    $detail = UnusedModel::findFirst($ref->getRid());
                    $attachment = $detail->getAttachment();
                    $filename = CNTY_ROOT_DIR.DS.$attachment;
                    if (file_exists($filename)) {
                        //在这里文件要是不存在无所谓
                        Filesystem::deleteFile($filename);
                    }
                     $detail->delete();
                    $events->fire('filerefmanager:clearUnusedFileRefs', $this, array($ref, $detail));
                }
                $db->commit();
            } catch (\Exception $ex) {
                $errorType = CoreErrorType::getInstance();
                $db->rollback();
                Kernel\throw_exception($ex, $errorType);
            }
        }
    }

    /**
     * 获取文件引用分表名称
     * 
     * @param int $tableId
     * @return string
     */
    protected function getRefInfoModelCls($tableId)
    {
        if (self::UNUSED_TABLE_ID == $tableId) {
            return self::UNUSED_M_CLS;
        }
        return sprintf(self::REF_INFO_M_CLS, $tableId);
    }

    /**
     * 根据文件的名称获取系统附件的地址
     * 
     * @param string $filename 
     * @return string
     */
    public function getAttachmentFilename($filename)
    {
        if (PHP_OS == \Cntysoft\WINDOWS) {
            $dirname = StdDir::getStdUploadDir().DS.date('Y'.DS.DS.'m'.DS.DS.'d');
        } else {
            $dirname = StdDir::getStdUploadDir().DS.date('Y'.DS.'m'.DS.'d');
        }
        if (!file_exists($dirname)) {
            Filesystem::createDir($dirname, 0755, true);
        }
        $ext = Kernel\get_file_ext($filename);
        $filename = md5($filename.time()).'.'.$ext;
        return Kernel\real_path($dirname.DS.$filename);
    }

}