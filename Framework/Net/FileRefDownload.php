<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net;
use Christ\Framework\Core\FileRef\Manager;
use Zend\Uri\Http as HttpUrl;
use Cntysoft\Stdlib\Filesystem;
use Cntysoft\Kernel;
/**
 * 文件下载类定义, 这个下载是程序自动下载远端文件， 一般下载图片比较多
 * 
 */
class FileRefDownload
{
    /**
     * @var \Christ\Framework\Core\FileRef\Manager $refManager
     */
    protected $refManager = null;
    /**
     * The curl session handle
     *
     * @var resource|null
     */
    protected $curl = null;
    /**
     * 超时时间
     * 
     * @var int $timeout
     */
    protected $timeout = 120;

    public function __construct()
    {
        if (!extension_loaded('curl')) {
            Kernel\throw_exception(new Exception(
                    Kernel\StdErrorType::msg('E_EXTENSION_NOT_LOADED', 'curl'), Kernel\StdErrorType::code('E_EXTENSION_NOT_LOADED')), \Cntysoft\STD_EXCEPTION_CONTEXT);
        }
        $this->refManager = new Manager();
    }

    /**
     * 下载指定的网络文件到系统文件目录
     * 
     * @param string $fileUrl
     */
    public function download($fileUrl)
    {
        if (null == $this->curl) {
            $this->curl = curl_init();
            curl_setopt($this->curl, CURLOPT_VERBOSE, 1);
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
        }
        $orgTime = ini_get('max_execution_time');
        try {
            $url = new HttpUrl($fileUrl);
            $attachmentFilename = Manager::getAttachmentFilename($url->getPath());
            curl_setopt($this->curl, CURLOPT_URL, $fileUrl);
            $fd = Filesystem::fopen($attachmentFilename, 'wb');
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->curl, CURLOPT_FILE, $fd);
            set_time_limit(0);
            if (!curl_exec($this->curl)) {
                curl_close($this->curl);
                $errorType = ErrorType::getInstance();
                Kernel\throw_exception(new Exception(
                        $errorType->msg('E_CURL_ERROR', curl_error($this->curl)), $errorType->code('E_CURL_ERROR')), $errorType);
            }else {
                curl_close($this->curl);
            }
            $fileinfo = stat($attachmentFilename);
            $refInfo = array(
               'filename' => Filesystem::basename($url->getPath()),
               'filesize' => $fileinfo['size'],
               'attachment' => str_replace(CNTY_ROOT_DIR, '', $attachmentFilename)
            );
            $rid = $this->refManager->addTempFileRef(null, $refInfo);
            $refInfo['rid'] = $rid;
            $refInfo['targetFile'] = $attachmentFilename;
            Filesystem::fclose($fd);
            set_time_limit($orgTime);
            return $refInfo;
        } catch (\Exception $ex) {
            set_time_limit($orgTime);
            //删除文件
            if (file_exists($attachmentFilename)) {
                Filesystem::deleteFile($attachmentFilename);
            }
            throw $ex;
        }
    }

    public function __destruct()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
        $this->curl = null;
    }
}