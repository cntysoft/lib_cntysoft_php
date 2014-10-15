<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs;
use Cntysoft\Stdlib\Filesystem;
use Cntysoft\Kernel;
/**
 * 标签的相关操作类
 */
class TagRefl
{
    /**
     * tag相关的目录名称
     */
    const TAG_DEF_FILE = 'Definition.php';
    const TAG_SCRIPT_FILE = 'Script.php';
    const LABEL_DEFAULT_TPL_NAME = 'Default';
    //const TAG_AJAX_SCRIPT_FILE = 'AjaxScript.php';
    const TAG_TPL_FILE_EXT = '.phtml';
    const TAG_AJAX_TPL_FILE_EXT = '.Ajax.phtml';
    const TAG_LABEL_BASE_CLASS_WITH_NS = 'Cntysoft\Framework\Qs\Engine\Tag\AbstractLabelScript';
    const TAG_AJAX_METHOD_SUFFIX = 'Async'; //标签的AJAX调用后台响应函数的后缀
    const TAG_LABLE_BASE_CLASS = 'AbstractLabelScript';
    const TAG_DS_BASE_CLASS_WITH_NS = 'Cntysoft\Framework\Qs\Engine\Tag\AbstractDsScript';
    const TAG_DS_BASE_CLASS = 'AbstractDsScript';
    const TAG_UI_IMAGE_DIR_NAME = 'Images';
    const TAG_UI_DIR_NAME = 'Ui';
    const TAG_UI_CSS_DIR_NAME = 'Css';
    const TAG_UI_JS_DIR_NAME = 'Js';
    const TAG_LIB_DIR_NAME = 'Lib';
    const TAG_LANG_DIR_NAME = 'Lang';
    const TAG_BASE_LABEL_NS = 'TagLibrary\Label';
    const TAG_BASE_DS_NS = 'TagLibrary\Ds';
    const TAG_BASE_BUILDIN_NS = 'Qs\Lib';
    const FILE_MODE = 0777; /* rwxrwxrwx */
    /**
     * 标签种类
     */
    const T_DS = 'Ds';
    const T_LABLE = 'Label';
    const T_BUILDIN = 'BuildIn';
    /**
     * 标签列表缓存
     * 
     * @var array  $tagListCache
     */
    protected static $tagListCache = array();
    
    /**
     * @var boolean $initialize 判断是否初始化
     */
    protected static $initialize = false;

    /**
     * @var array $labelRequireFields label标签的元信息必要字段
     */
    protected static $labelRequireFields = null;
    /**
     * @var array $staticLabelRequireFields 静态标签元信息必要字段
     */
    protected static $staticLabelRequireFields = null;
    /**
     * @var array $dsRequireFields 数据源标签属性的必要字段
     */
    protected static $dsRequireFields = null;
    /**
     * @var string 标签文件夹路径
     */
    protected static $tagDir = null;
    
    /**
     * 扫描标签文件夹，获取标签的分类
     * 
     * @param array $tagTypes 需要探测的标签类型
     * @return array
     */
    public static function getTagList(array $tagTypes = array())
    {
        if (empty(self::$tagListCache)) {
            $ret = array();
            $errorType = ErrorType::getInstance();
            $selfCls = '\\'.get_class();
            $path = CNTY_TAG_DIR.DS;
            $tagPath = '';
            foreach ($tagTypes as $tagType) {
                $ret[$tagType] = array();
                $cur = &$ret[$tagType];
                $tagPath = Kernel\real_path($path.$tagType);
                $category = '';
                Filesystem::traverseFs($tagPath, 2, function($fileinfo, $depth)use(&$cur, &$category, $selfCls, $errorType) {
                            if (0 == $depth) {
                                //分类
                                $category = $fileinfo->getFilename();
                                $cur[$category] = array();
                            } elseif (1 == $depth) {
                                //判断定义文件是否存在
                                $tagDefFile = $fileinfo->getPathname().DS.$selfCls::TAG_DEF_FILE;
                                if (!file_exists($tagDefFile)) {
                                    Kernel\throw_exception(new Exception(
                                        $errorType->msg('E_TAG_DEF_FILE_NOT_EXIST'),
                                        $errorType->code('E_TAG_DEF_FILE_NOT_EXIST')
                                        ), $errorType);
                                }
                                $cur[$category][$fileinfo->getFilename()] = @include $tagDefFile;
                            }
                        });
            }
            self::$tagListCache = $ret;
        }
        return self::$tagListCache;
    }
    
     /**
     * @param string $tagType
     * @param string classify
     * @param string $tagName 如果标签名称
     */
    public static function checkTagExist($tagType, $classify, $tagName, $needExist = true)
    {
        self::checkTagTypes($tagType);
        //判断分类是否存在
        $list = self::getTagList(array($tagType));
        $list = $list[$tagType];
        $classify = Kernel\real_path($classify);
        if (!isset($list[$classify])) {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                $errorType->msg('E_TAG_CATEGORY_NOT_EXIST', $classify), $errorType->code('E_TAG_CATEGORY_NOT_EXIST')
                ), $errorType);
        }
        $list = $list[$classify];
        $tagName = Kernel\real_path($tagName);
        if (!$needExist && isset($list[$tagName])) {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                $errorType->msg('E_TAG_EXIST', $tagName), $errorType->code('E_TAG_EXIST')
                ), $errorType);
        }else if($needExist && !isset($list[$tagName])){
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                $errorType->msg('E_TAG_NOT_EXIST', $tagName), $errorType->code('E_TAG_NOT_EXIST')
                ), $errorType);
        }
    }
     /**
     * 删除一个指定的标签
     * 
     * @param String $tagType
     * @param String 
     */
    public static function deleteTag($tagType, $classify, $tagName)
    {
        self::checkTagTypes($tagType);
        //删除不需要判断， 不存在什么伤害都没有
        $target = Kernel\real_path(CNTY_TAG_DIR.DS.$tagType.DS.$classify. DS .$tagName);
        if(file_exists($target)){
           Filesystem::deleteDirRecusive($target);
        }
    }
    /**
     * 删除一个分类
     * 
     * @param array $params
     */
    public static function deleteClassify($tagType, $classify)
    {
        self::checkTagTypes($tagType);
        //删除不需要判断， 不存在什么伤害都没有
        $target = CNTY_TAG_DIR.DS.$tagType.DS.$classify;
        if(file_exists($target)){
           Filesystem::deleteDirRecusive($target);
        }
    }
    /**
     * 更新一个指定的标签信息
     * 
     * @param string $tagType
     * @param string $sourceClassify
     * @param string $sourceTagName
     * @param array $meta
     */
    public static function updateLabelTagMeta($tagType, $sourceClassify, $sourceTagName, array $meta)
    {
        self::init();
        //查看来源是否合法
        self::checkTagExist($tagType, $sourceClassify, $sourceTagName, true);
        //检查目标
        if(!isset($meta['static'])){
            $meta['static'] = false;//默认为动态标签
        }else{
            $meta['static'] = (boolean)$meta['static'];
        }
        if($meta['static']){
             Kernel\ensure_array_has_fields($meta, self::$staticLabelRequireFields);
        }else{
             Kernel\ensure_array_has_fields($meta, self::$labelRequireFields);
        }
        self::updateTagMeta($tagType, $sourceClassify, $sourceTagName, $meta);
    }
    /**
     * 更新一个指定的标签信息
     * 
     * @param string $tagType
     * @param string $sourceClassify
     * @param string $sourceTagName
     * @param array $meta
     */
    public static function updateDsTagMeta($tagType, $sourceClassify, $sourceTagName, array $meta)
    {
        self::init();
        //查看来源是否合法
        self::checkTagExist($tagType, $sourceClassify, $sourceTagName, true);
        //检查目标
        Kernel\ensure_array_has_fields($meta, array('id', 'category', 'class', 'namespace'));
        self::updateTagMeta($tagType, $sourceClassify, $sourceTagName, $meta);
    }
    
    /**
     * 跟新标签内容的实际操作
     * 
     * @param type $tagType
     * @param type $sourceClassify
     * @param type $sourceTagName
     * @param array $meta
     */
    protected static function updateTagMeta($tagType, $sourceClassify, $sourceTagName, array $meta)
    {
        $base = CNTY_TAG_DIR.DS.$tagType;
        $sourceTagPath = $base.DS.$sourceClassify.DS.$sourceTagName;
        $targetTagPath = $base.DS.$meta['category'].DS.$meta['id'];
        //在分类不一样的时候进行检查
        if($sourceClassify !== $meta['category'] || $sourceTagName !== $meta['id']){
            self::checkTagExist($tagType, $meta['category'], $meta['id'], false);
            //改变名字
            Filesystem::rename($sourceTagPath, $targetTagPath);
        }
        //保存定义文件
       self::writeMetaInfo($meta, $targetTagPath);
    }
    /**
     * 复制一个标签
     * 
     * @param string $tagType
     * @param string $classify
     * @param string $tagName
     */
    public static function copyTag($tagType, $classify, $tagName)
    {
        self::checkTagExist($tagType, $classify, $tagName, true);
        //保证标签文件夹名称唯一
        $source = CNTY_TAG_DIR.DS.$tagType.DS.$classify.DS.$tagName;
        Filesystem::copyDir($source, Filesystem::dirname($source));
        $meta = self::getTagMeta($tagType, $classify, $tagName);
        //这里好使用 '_副本'是与Filesystem中的copyDir生成新目录名方法相对应的
        $meta['id']  = $tagName.'_副本';
        self::writeMetaInfo($meta, $source.'_副本');
    }
    /**
     * 根据 $bclassify 修改或新加一个分类
     * 
     * @param type $tagType
     * @param type $classify
     * @param type $bclassify
     */
    public static function classifyChange($tagType, $classify, $bclassify)
    {
        if('' === $bclassify){
            $source = CNTY_TAG_DIR.DS.$tagType.DS.$classify;
            Filesystem::createDir($source);
        }else{
            $target = CNTY_TAG_DIR.DS.$tagType.DS.$classify;
            $source = CNTY_TAG_DIR.DS.$tagType.DS.$bclassify;
            Filesystem::rename($source, $target);
        }
    }
    
    /**
     * 创建Label标签的骨架
     * 
     * @param array $meta
     */
    public static function createLabelTagSkeleton(array $meta)
    {
        //需要在有异常的时候回滚吗？
        self::init();
        if(!isset($meta['static'])){
            $meta['static'] = false;//默认为动态标签
        }else{
            $meta['static'] = (boolean)$meta['static'];
        }
        if($meta['static']){
             Kernel\ensure_array_has_fields($meta, self::$staticLabelRequireFields);
        }else{
             Kernel\ensure_array_has_fields($meta, self::$labelRequireFields);
        }
        //判断标签是否存在
        self::checkTagExist(self::T_LABLE, $meta['category'], $meta['id'], false);
        $baseDir = CNTY_TAG_DIR.DS.self::T_LABLE.DS.$meta['category'];
        //创建栏目结构
        $dir = $baseDir.DS.$meta['id'];
        try {
            Filesystem::createDir($dir, self::FILE_MODE);
            self::writeMetaInfo($meta, $dir);
            if(!$meta['static']){
                self::createLabelSubDirs($dir);
                self::generateScriptClass($meta, $dir, self::T_LABLE);
                //Filesystem::touch($dir.DS.self::TAG_AJAX_SCRIPT_FILE);
                //Filesystem::touch($dir.DS.self::LABEL_DEFAULT_TPL_NAME.self::TAG_AJAX_TPL_FILE_EXT);
            }
            //创建标签模板文件
            Filesystem::touch($dir.DS.self::LABEL_DEFAULT_TPL_NAME.self::TAG_TPL_FILE_EXT);
            //这里需要删除模板引擎的缓存
        } catch (\Exception $e) {
            Filesystem::deleteDirRecusive($dir);
            throw $e;
        }
    }

    /**
     * 创建数据源标签的骨架
     * 
     * @param Array $meta
     */
    public static function createDsTagSkeleton(array $meta)
    {
        //需要在有异常的时候回滚吗？
        self::init();
        Kernel\ensure_array_has_fields($meta, array('id', 'category', 'class', 'namespace'));
        //判断标签是否存在
        self::checkTagExist(self::T_DS, $meta['category'], $meta['id'], false);
        $baseDir = CNTY_TAG_DIR.DS.self::T_DS.DS.$meta['category'];
        //创建栏目结构
        $dir = $baseDir.DS.$meta['id'];

        try {
            Filesystem::createDir($dir, self::FILE_MODE);
            self::writeMetaInfo($meta, $dir);
            self::generateScriptClass($meta, $dir, self::T_DS);
            //这里需要删除模板引擎的缓存
        } catch (\Exception $e) {
            Filesystem::deleteDirRecusive($dir);
            throw $e;
        }
    }
    /**
     * 
     * @param type $tagType
     * @param type $classify
     * @param type $tagName
     */
    public static function checkTagClassExist($tagType, $classify, $tagClass)
    {
        self::checkTagTypes($tagType);
        //判断分类是否存在
        $list = self::getTagList(array($tagType));
        $list = $list[$tagType];
        $classify = Kernel\real_path($classify);
        if (!isset($list[$classify])) {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                $errorType->msg('E_TAG_CATEGORY_NOT_EXIST', $classify), $errorType->code('E_TAG_CATEGORY_NOT_EXIST')    
                ), $errorType);
        }
        $list = $list[$classify];
        if(!empty($list)){
            foreach($list as $val){
                if(isset($val['class'])){                    
                    if($tagClass == $val['class']){
                        $errorType = ErrorType::getInstance();
                        Kernel\throw_exception(new Exception(
                            $errorType->msg('E_TAG_CLASS_EXIT', $tagClass), $errorType->code('E_TAG_CLASS_EXIT')    
                            ), $errorType);
                    }  
                }
            }
        }
    }
    
    /**
     * 获取指定标签的定义类名称
     * 
     * @param string $tagType
     * @param string $classify
     * @param string $tagName
     * @return string
     */
    public static function getTagCls($tagType, $classify, $tagName)
    {
        self::checkTagExist($tagType, $classify, $tagName, true);
        $meta = self::getTagMeta($tagType, $classify, $tagName);
        if(self::T_DS == $tagType){
            $baseNs = self::TAG_BASE_DS_NS;
        }else if(self::T_LABLE == $tagType){
            $baseNs = self::TAG_BASE_LABEL_NS;
        }
        return $baseNs.'\\'.$meta['namespace'].'\\'.$meta['class'];
    }
    
    /**
     * 初始化自己
     */
    protected static function init()
    {
        if (!self::$initialize) {
            self::$labelRequireFields = array(
               'id', 'category', 'class', 'namespace', 'static'
            );
            self::$staticLabelRequireFields = array(
               'id', 'category','static'
            );
            self::$initialize = true;
        }
    }

    /**
     * 将meta信息写入即将创建的标签的信息中
     * 
     * @param array $meta
     * @param string $dir 即将需要创建的标签的目录
     */
    protected static function writeMetaInfo(array &$meta, $dir)
    {
        $filename = $dir.DS.self::TAG_DEF_FILE;
        $data = "<?php\nreturn ".var_export($meta, true).';';
        Filesystem::filePutContents($filename, $data);
    }
    
    /**
     * 创建label类动态标签的栏目结构
     * 
     * @param array $meta
     */
    protected static function createLabelSubDirs($baseDir)
    {
        $dirs = array(
           self::TAG_UI_CSS_DIR_NAME,
           self::TAG_UI_IMAGE_DIR_NAME,
           self::TAG_UI_JS_DIR_NAME,
           self::TAG_LIB_DIR_NAME
        );
        foreach($dirs as $dir){
            Filesystem::createDir($baseDir.DS.$dir, self::FILE_MODE, true);
        }
    }
    /**
     * 获取标签元信息
     * 
     * @param string $tagType
     * @param string $classify
     * @param string $tagName
     * @return array
     * @throw Exception\RuntimeException
     */
    public static function getTagMeta($tagType, $classify, $tagName)
    {
        self::checkTagExist($tagType, $classify, $tagName, true);
        $list = self::getTagList(array($tagType));
        $classify = Kernel\real_path($classify);
        $tagName = Kernel\real_path($tagName);
        return $list[$tagType][$classify][$tagName];
    }
    /**
     * 判断一个分类是否存在
     * 
     * @param string $tagType
     * @param string $classify
     * @return boolean
     * @throw Exception\InvalidArgumentException
     */
    public static function classifyExist($tagType, $classify)
    {
        self::checkTagTypes($tagType);
        return in_array($classify, self::getTagClassifies($tagType));
    }
    /**
     * 检查标签类型是否合法
     * 
     * @param string $tagType
     */
    public static function checkTagTypes($tagType)
    {
        if(!in_array($tagType, self::getTagTypes())){
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                    $errorType->msg('E_TAG_TYPE_NOT_SUPPORT', $tagType),
                    $errorType->code('E_TAG_TYPE_NOT_SUPPORT')
            ), $errorType);
        }
    }
    /**
     * 获取标签分类
     *
     * @param string $type 标签的种类
     * @return array
     */
    public static function getTagClassifies($type = self::T_LABLE)
    {
        self::checkTagTypes($type);
        $path = Kernel\real_path(CNTY_TAG_DIR.DS.$type);
        $ret = array();
        Filesystem::traverseFs($path, 1, function($fileinfo)use(&$ret){
            $ret[] = $fileinfo->getFilename();
        });
        return $ret;
    }
    
    /**
     * 参数标签的类文件script.php
     * 
     * @param array $meta
     * @param type $baseDir
     */
    protected static function generateScriptClass(array &$meta, $baseDir, $flag = self::T_LABLE)
    {
        $classname = $meta['class'];
        $ns = $meta['namespace'];
        $separator = DS;
        if(self::T_LABLE === $flag){
            $tagBaseNs = self::TAG_BASE_LABEL_NS;
            $tagBaseClass = self::TAG_LABLE_BASE_CLASS;
            $tagBaseClassWithNs = self::TAG_LABEL_BASE_CLASS_WITH_NS;
        }elseif(self::T_DS === $flag){
            $tagBaseNs = self::TAG_BASE_DS_NS;
            $tagBaseClass = self::TAG_DS_BASE_CLASS;
            $tagBaseClassWithNs = self::TAG_DS_BASE_CLASS_WITH_NS;
        }
        
        $content = <<<classContent
<?php
    namespace $tagBaseNs$separator$ns;
    use $tagBaseClassWithNs;
    
    class $classname extends $tagBaseClass
    {
        
    }
classContent;
        Filesystem::filePutContents($baseDir.DS.self::TAG_SCRIPT_FILE, $content);
    }
    
    /**
     * 获取系统内置的标签类名称
     * 
     * @param string $classify
     * @return string
     */
    public static function getBuildInTagCls($classify)
    {
        return self::TAG_BASE_BUILDIN_NS.'\\'.$classify;
    }
    
    /**
     * 获取标签的类型
     * 
     * @return array
     */
    public static function getTagTypes()
    {
        return array(
           self::T_DS,
           self::T_LABLE,
           self::T_BUILDIN
        );
    }
}