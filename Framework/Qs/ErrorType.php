<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;
/**
 * 模板引擎的错误处理信息类
 */
class ErrorType extends BaseErrorType
{
    /**
     * @inheritDoc
     */
    protected $map = array(
       'E_TPL_FILE_NOT_EXIST' => array(10000, 'template %s is not exist'),
       'E_LEAK_REQUIRE_PARAMS' => array(10001, 'tag leak require params : %s'),
       'E_TAG_NOT_EXIST' => array(10002, 'tag %s  is not exist'),
       'E_TAG_META_NOT_EXIST' => array(10003, ' meta file is not exist'),
       'E_TAG_META_ERROR' => array(10004, ' meta parse error : %s'),
       'E_TAG_REQUIRE_PATAM_NOT_EXIST' => array(10009, 'tag require params : %s'),
       'E_TAG_SCRIPT_FILE_NOT_EXIST' => array(10010, ' script file not exist'),
       'E_TAG_TPL_FILE_NOT_EXIST' => array(10011, ' template file not exist'),
       'E_TAG_CLS_NOT_EXIST' => array(10012, ' class %s not exist'),
       'E_TAG_TYPE_NOT_SUPPORT' => array(10013, 'tag type : %s is not supported '),
       'E_TAG_DEF_FILE_NOT_EXIST' => array(10014, 'the define of the tag is not exit'),
       'E_TAG_EXIST' => array(10015, 'tag %s is exit'),
       'E_TAG_CATEGORY_NOT_EXIST' => array(10016, 'tag category is not exit'),
       'E_TAG_CLASS_EXIT' => array(10017, 'class %s is exit'),
       'E_TAG_DIR_FINDER_NOT_SET' => array(10018, 'tag dir finder is not set')
    );
 }