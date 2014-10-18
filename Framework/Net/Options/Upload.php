<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\Options;
use Cntysoft\Stdlib\AbstractOption;
use Cntysoft\Kernel\ConfigProxy;
use Cntysoft\Kernel\StdDir;
use Cntysoft\Kernel\StdErrorType;
use Cntysoft\Framework\Net\Exception;
use Cntysoft\Stdlib\Filesystem;

/**
 * 文件上传处理类配置对象
 */
class Upload extends AbstractOption
{
    protected static $allowDirs = array(
       CNTY_UPLOAD_DIR
    );
    /**
     * 这个应该探测
     * 
     * @var int $maxFileSize
     */
    protected $maxFileSize = 104857600; //默认100M
    /**
     * 允许的文件类型, 以后这个可以从配置中读取
     * 
     * @var array $allowFileTypes
     */
    protected $allowFileTypes = array('jpg', 'jpeg', 'gif', 'png', 'doc', 'docx', 'rar', 'zip', '.tar.gz', '.bz2', 'html', 'css', 'js');
    /**
     * 上传文件目标文件夹
     *  
     * @var string $uploadDir
     */
    protected $uploadDir;
    /**
     * 文件存在是否直接覆盖
     * 
     * @var boolean $overwrite
     */
    protected $overwrite = false;
    /**
     * 是否给文件名称加上一串随机码
     * 
     * @var boolean $randomize
     */
    protected $randomize = false;
    /**
     * 是否根据日期创建子文件夹
     * 
     * @var boolean $createSubDir
     */
    protected $createSubDir = false;
    /**
     * 指定的上传之后的文件名称
     * 
     * @var string $targetName
     */
    protected $targetName = null;
    /**
     * 当前的分段ID
     * 
     * @var int  $chunk
     */
    protected $chunk = null;
    /**
     * 总共的分段数
     * 
     * @var int $totalChunk
     */
    protected $totalChunk = null;
    /**
     * 是否开启文件引用追踪
     * 
     * @var boolean $enableFileRef
     */
    protected $enableFileRef;

    /**
     * 上传组件配置信息
     * 
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $netConfig = ConfigProxy::getFrameworkConfig('Net', ConfigProxy::C_TYPE_FRAMEWORK_SYS);
        $swfConfig = isset($netConfig->swfuploader) ? $netConfig->swfuploader->toArray(): array();
        $options += $swfConfig;
        self::$allowDirs[] = CNTY_DATA_DIR . DS . 'App' . DS . 'Cms' . DS . 'ContentModelManager' . DS . 'Image';
        $options['totalChunk'] = $options['total_chunk'];
        unset($options['total_chunk']);
        parent::__construct($options);
        //字段相容性
        if ($this->getTargetName()) {
            $this->setRandomize(false);
        }
    }

    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    public function getAllowFileTypes()
    {
        return $this->allowFileTypes;
    }

    public function setAllowFileTypes($allowFileTypes)
    {
        $this->allowFileTypes = $allowFileTypes;
    }

    public function getUploadDir()
    {
        /**
         * 这个写法很危险
         */
//        if (null == $this->uploadDir) {
//            $path = StdDir::getUploadDir();
//        } else {
//            $path = $this->uploadDir;
//        }
        if(null == $this->uploadDir) {
            throw new Exception(
                StdErrorType::msg('E_UPLOAD_PATH_EMPTY'), StdErrorType::code('E_UPLOAD_PATH_EMPTY')
            );
        }
        $path = $this->uploadDir;
        if ($this->createSubDir) {
            if (PHP_OS == \Cntysoft\WINDOWS) {
                $path .= DS . date('Y' . DS . DS . 'm' . DS . DS . 'd');
            } else {
                $path .= DS . date('Y' . DS . 'm' . DS . 'd');
            }
            if (!file_exists($path)) {
                Filesystem::createDir($path, 0755, true);
            }
        }
        return $path;
    }

    public function getCreateSubDir()
    {
        return $this->createSubDir;
    }

    public function setCreateSubDir($createSubDir)
    {
        $this->createSubDir = $createSubDir;
    }

    public function setUploadDir($uploadDir)
    {
        $flag = false;
        foreach (self::$allowDirs as $dir) {
            if ($dir == substr($uploadDir, 0, strlen($dir))) {
                $flag = true;
                break;
            }
        }
        if (!$flag) {
            throw new Exception(
            StdErrorType::msg('E_UPLOAD_PATH_NOT_EXIST', $uploadDir), StdErrorType::code('E_UPLOAD_PATH_NOT_EXIST')
            );
        }
        $this->uploadDir = $uploadDir;
    }

    public function getEnableFileRef()
    {
        return $this->enableFileRef;
    }

    public function setEnableFileRef($enableFileRef)
    {
        $this->enableFileRef = $enableFileRef;
        return $this;
    }

    public function getEnableNail()
    {
        return $this->enableNail;
    }

    public function setEnableNail($enableNail)
    {
        $this->enableNail = $enableNail;
        return $this;
    }

    public function getOverwrite()
    {
        return $this->overwrite;
    }

    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
    }

    public function getRandomize()
    {
        return $this->randomize;
    }

    public function setRandomize($randomize)
    {
        $this->randomize = $randomize;
    }

    public function setTargetName($targetName)
    {
        $this->targetName = $targetName;
    }

    public function getTargetName()
    {
        return $this->targetName;
    }

    public function setChunk($chunk)
    {
        $this->chunk = (int) $chunk;
    }

    public function getChunk()
    {
        return $this->chunk;
    }

    public function setTotalChunk($num)
    {
        $this->totalChunk = (int) $num;
    }

    public function getTotalChunk()
    {
        return $this->totalChunk;
    }

    /**
     * @return array 
     */
    public static function getAllowDirs()
    {
        return self::$allowDirs;
    }

}